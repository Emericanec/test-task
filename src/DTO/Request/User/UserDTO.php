<?php

declare(strict_types=1);

namespace App\DTO\Request\User;

use \App\DTO\Request\RequestDTOInterface;
use Symfony\Component\HttpFoundation\Request;

class UserDTO implements RequestDTOInterface
{
    /** @var null|string  */
    private $firstName;

    /** @var null|string  */
    private $lastName;

    /** @var null|string  */
    private $email;

    /** @var null|int  */
    private $parentId;

    public function __construct(Request $request)
    {
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
