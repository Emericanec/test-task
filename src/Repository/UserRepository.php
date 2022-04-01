<?php

declare(strict_types=1);

namespace App\Repository;

use App\DTO\Request\User\AbstractUserDTO;
use App\Entity\User;
use App\Exception\User\CreateOrUpdateUserException;
use App\Exception\User\ParentUserLimitExceededException;
use App\Exception\User\ParentUserNotFoundException;
use App\Exception\User\UserNotFoundException;
use DateTime;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\ORM\OptimisticLockException;
use Doctrine\ORM\ORMException;
use Doctrine\Persistence\ManagerRegistry;
use Exception;
use Symfony\Component\Validator\Validator\ValidatorInterface;

class UserRepository extends ServiceEntityRepository
{
    /**
     * @var ValidatorInterface
     */
    private $validator;

    public function __construct(ManagerRegistry $registry, ValidatorInterface $validator)
    {
        $this->validator = $validator;

        parent::__construct($registry, User::class);
    }

    /**
     * @param AbstractUserDTO $dto
     * @param int $appId
     * @param int|null $id
     *
     * @return User
     * @throws CreateOrUpdateUserException
     * @throws ParentUserLimitExceededException
     * @throws ParentUserNotFoundException
     * @throws UserNotFoundException
     */
    public function createOrUpdate(AbstractUserDTO $dto, int $appId, int $id = null): User
    {
        $user = null === $id ? new User() : $this->findOneBy(['id' => $id, 'appId' => $appId, 'deletedAt' => null]);
        if (null === $user) {
            throw new UserNotFoundException();
        }

        $user->setAppId($appId);
        $user->setFirstName($dto->getFirstName() ?? $user->getFirstName());
        $user->setLastName($dto->getLastName() ?? $user->getLastName());
        $user->setEmail($dto->getEmail() ?? $user->getEmail());
        if (null !== $dto->getParentId()) {
            /** @var null|User $parentUser */
            $parentUser = $this->findOneBy(['id' => $dto->getParentId(), 'appId' => $user->getAppId()]);
            if (null === $parentUser) {
                throw new ParentUserNotFoundException();
            } elseif (null !== $parentUser->getParentUser()) {
                throw new ParentUserLimitExceededException();
            }

            $user->setParentUser($parentUser);
        }

        try {
            $errors = $this->validator->validate($user);
            if (0 < $errors->count()) {
                $error = $errors->get(0);
                throw new Exception("{$error->getPropertyPath()} {$error->getMessage()}");
            }

            $em = $this->getEntityManager();
            $em->persist($user);
            $em->flush();
        } catch (Exception $exception) {
            throw new CreateOrUpdateUserException($exception->getMessage(), $exception->getCode(), $exception);
        }

        return $user;
    }

    /**
     * @param int $id
     * @param int $appId
     *
     * @return bool
     * @throws UserNotFoundException
     * @throws ORMException
     * @throws OptimisticLockException
     */
    public function deleteOne(int $id, int $appId): bool
    {
        /** @var User $user */
        $user = $this->findOneBy(['id' => $id, 'appId' => $appId]);
        if (null === $user) {
            throw new UserNotFoundException();
        }

        $user->setDeletedAt(new DateTime('now'));

        $em = $this->getEntityManager();
        $em->persist($user);
        $em->flush();

        return true;
    }
}
