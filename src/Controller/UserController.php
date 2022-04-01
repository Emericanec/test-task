<?php

declare(strict_types=1);

namespace App\Controller;

use App\Controller\Validator\AbstractRequestValidator;
use App\Controller\Validator\CreateUserRequestRequestValidator;
use App\Controller\Validator\UpdateUserRequestRequestValidator;
use App\DTO\Request\User\UserDTO;
use App\Entity\Application;
use App\Entity\User;
use App\Exception\User\CreateOrUpdateUserException;
use App\Exception\User\ParentUserLimitExceededException;
use App\Exception\User\ParentUserNotFoundException;
use App\Exception\User\UserNotFoundException;
use App\Repository\UserRepository;
use Exception;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Validator\Validator\ValidatorInterface;

class UserController extends AbstractController implements TokenAuthenticatedControllerInterface
{
    /**
     * @Route("/user/{id}", methods={"GET"})
     * @param int $id
     * @param UserRepository $userRepository
     * @param Request $request
     *
     * @return JsonResponse
     */
    public function one(int $id, UserRepository $userRepository, Request $request): Response
    {
        $application = $this->getAuthApplication($request);
        /** @var null|User $user */
        $user = $userRepository->findOneBy(['id' => $id, 'appId' => $application->getId(), 'deletedAt' => null]);
        if (null === $user) {
            return new JsonResponse(['message' => 'User not found'], JsonResponse::HTTP_NOT_FOUND);
        }

        return new JsonResponse(['user' => $user->toArray()], JsonResponse::HTTP_OK);
    }

    /**
     * @Route("/user", methods={"POST"})
     * @param Request $request
     * @param UserRepository $userRepository
     * @param ValidatorInterface $validator
     *
     * @return Response
     */
    public function create(Request $request, UserRepository $userRepository, ValidatorInterface $validator): Response
    {
        $requestValidator = new CreateUserRequestRequestValidator($request, $validator);

        return $this->createOrUpdate($request, $requestValidator, $userRepository);
    }

    /**
     * @Route("/user/{id}", methods={"PUT"})
     * @param int $id
     * @param UserRepository $userRepository
     * @param Request $request
     * @param ValidatorInterface $validator
     *
     * @return JsonResponse
     */
    public function update(int $id, UserRepository $userRepository, Request $request, ValidatorInterface $validator): Response
    {
        $requestValidator = new UpdateUserRequestRequestValidator($request, $validator);

        return $this->createOrUpdate($request, $requestValidator, $userRepository, $id);
    }

    /**
     * @Route("/user/{id}", methods={"DELETE"})
     * @param int $id
     * @param UserRepository $userRepository
     * @param Request $request
     *
     * @return Response
     */
    public function delete(int $id, UserRepository $userRepository, Request $request): Response
    {
        try {
            $userRepository->deleteOne($id, $this->getAuthApplication($request)->getId());
        } catch (UserNotFoundException $e) {
            return new JsonResponse(['message' => 'User not found'], JsonResponse::HTTP_NOT_FOUND);
        } catch (Exception $e) {
            return new JsonResponse(null, JsonResponse::HTTP_INTERNAL_SERVER_ERROR);
        }

        return new JsonResponse();
    }

    /**
     * @param Request $request
     *
     * @return Application
     */
    public function getAuthApplication(Request $request): UserInterface
    {
        return $request->attributes->get('auth_application');
    }

    protected function createOrUpdate(Request $request, AbstractRequestValidator $requestValidator, UserRepository $userRepository, int $id = null): Response
    {
        $violations = $requestValidator->validate();
        if (0 < $violations->count()) {
            $error = $violations->get(0);
            return new JsonResponse(
                ['message' => $error->getPropertyPath() . ' ' . $error->getMessage()],
                JsonResponse::HTTP_BAD_REQUEST
            );
        }

        try {
            $user = $userRepository->createOrUpdate(
                new UserDTO($request),
                $this->getAuthApplication($request)->getId(),
                $id
            );
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
