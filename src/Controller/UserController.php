<?php

declare(strict_types=1);

namespace App\Controller;

use App\DTO\Request\User\AbstractUserDTO;
use App\DTO\Request\User\CreateUserDTO;
use App\DTO\Request\User\UpdateUserDTO;
use App\Entity\User;
use App\Exception\User\CreateOrUpdateUserException;
use App\Exception\User\ParentUserLimitExceededException;
use App\Exception\User\ParentUserNotFoundException;
use App\Exception\User\UserNotFoundException;
use App\Repository\UserRepository;
use App\Request\TokenRequest;
use Exception;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Validator\Validator\ValidatorInterface;

class UserController extends AbstractController implements TokenAuthenticatedControllerInterface
{
    /**
     * @Route("/user/{id}", methods={"GET"})
     * @param int $id
     * @param TokenRequest $request
     *
     * @param UserRepository $userRepository
     *
     * @return JsonResponse
     */
    public function one(int $id, TokenRequest $request, UserRepository $userRepository): Response
    {
        /** @var null|User $user */
        $user = $userRepository->findOneBy(['id' => $id, 'appId' => $request->getAppId(), 'deletedAt' => null]);
        if (null === $user) {
            return new JsonResponse(['message' => 'User not found'], JsonResponse::HTTP_NOT_FOUND);
        }

        return new JsonResponse(['user' => $user->toArray()], JsonResponse::HTTP_OK);
    }

    /**
     * @Route("/user", methods={"POST"})
     * @param TokenRequest $request
     * @param UserRepository $userRepository
     * @param ValidatorInterface $validator
     *
     * @return Response
     */
    public function create(TokenRequest $request, UserRepository $userRepository, ValidatorInterface $validator): Response
    {
        $dto = new CreateUserDTO($request, $validator);

        return $this->createOrUpdate($request, $dto, $userRepository);
    }

    /**
     * @Route("/user/{id}", methods={"PUT"})
     * @param int $id
     * @param UserRepository $userRepository
     * @param TokenRequest $request
     * @param ValidatorInterface $validator
     *
     * @return JsonResponse
     */
    public function update(int $id, UserRepository $userRepository, TokenRequest $request, ValidatorInterface $validator): Response
    {
        $dto = new UpdateUserDTO($request, $validator);

        return $this->createOrUpdate($request, $dto, $userRepository, $id);
    }

    /**
     * @Route("/user/{id}", methods={"DELETE"})
     * @param int $id
     * @param UserRepository $userRepository
     * @param TokenRequest $request
     *
     * @return Response
     */
    public function delete(int $id, UserRepository $userRepository, TokenRequest $request): Response
    {
        try {
            $userRepository->deleteOne($id, $request->getAppId());
        } catch (UserNotFoundException $e) {
            return new JsonResponse(['message' => 'User not found'], JsonResponse::HTTP_NOT_FOUND);
        } catch (Exception $e) {
            return new JsonResponse(null, JsonResponse::HTTP_INTERNAL_SERVER_ERROR);
        }

        return new JsonResponse();
    }

    protected function createOrUpdate(TokenRequest $request, AbstractUserDTO $dto, UserRepository $userRepository, int $id = null): Response
    {
        try {
            $dto->validate();
        } catch (Exception $exception) {
            return new JsonResponse(
                ['message' => $exception->getMessage()],
                JsonResponse::HTTP_BAD_REQUEST
            );
        }

        try {
            $user = $userRepository->createOrUpdate($dto, $request->getAppId(), $id);
        } catch (UserNotFoundException $e) {
            return new JsonResponse(['message' => 'User not found'], JsonResponse::HTTP_NOT_FOUND);
        } catch (ParentUserNotFoundException $e) {
            return new JsonResponse(['message' => 'Parent user not found'], JsonResponse::HTTP_BAD_REQUEST);
        } catch (ParentUserLimitExceededException $e) {
            return new JsonResponse(['message' => 'Parent user must not have a parent'], JsonResponse::HTTP_BAD_REQUEST);
        } catch (CreateOrUpdateUserException $e) {
            return new JsonResponse(['message' => $e->getMessage()], JsonResponse::HTTP_INTERNAL_SERVER_ERROR);
        } catch (Exception $e) {
            return new JsonResponse(null, JsonResponse::HTTP_INTERNAL_SERVER_ERROR);
        }

        return new JsonResponse(['user' => $user->toArray()], JsonResponse::HTTP_OK);
    }
}
