<?php

declare(strict_types=1);

namespace App\DTO\Request\User;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Validator\Validator\ValidatorInterface;
use Symfony\Component\Validator\Constraints as Assert;

class CreateUserDTO extends AbstractUserDTO
{
    /**
     * @Assert\NotBlank
     * @var string|null
     */
    private $firstName;

    /**
     * @Assert\NotBlank
     * @var string|null
     */
    private $lastName;

    /**
     * @Assert\NotBlank
     * @Assert\Email
     * @var string|null
     */
    private $email;

    /**
     * @Assert\Type(type="integer")
     * @var int|null
     */
    private $parentId;

    public function __construct(Request $request, ValidatorInterface $validator)
    {
        $this->validator = $validator;

        $data = json_decode($request->getContent(), true);

        $this->firstName = $data['first_name'] ?? null;
        $this->lastName = $data['last_name'] ?? null;
        $this->email = $data['email'] ?? null;
        $this->parentId = $data['parent_id'] ?? null;
    }

    public function getFirstName(): ?string
    {
        return $this->firstName;
    }

    public function getLastName(): ?string
    {
        return $this->lastName;
    }

    public function getEmail(): ?string
    {
        return $this->email;
    }

    public function getParentId(): ?int
    {
        return $this->parentId;
    }
}
