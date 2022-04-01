<?php

declare(strict_types=1);

namespace App\DTO\Request\User;

use App\DTO\Request\WithValidationTrait;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Validator\Validator\ValidatorInterface;
use Symfony\Component\Validator\Constraints as Assert;

class UpdateUserDTO extends AbstractUserDTO
{
    /**
     * @Assert\Type(type="string")
     * @var string|null
     */
    private $firstName;

    /**
     * @Assert\Type(type="string")
     * @var string|null
     */
    private $lastName;

    public function __construct(Request $request, ValidatorInterface $validator)
    {
        $this->validator = $validator;

        $data = json_decode($request->getContent(), true);

        $this->firstName = $data['first_name'] ?? null;
        $this->lastName = $data['last_name'] ?? null;
    }

    public function getFirstName(): ?string
    {
        return $this->firstName;
    }

    public function getLastName(): ?string
    {
        return $this->lastName;
    }
}
