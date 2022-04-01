<?php

declare(strict_types=1);

namespace App\DTO\Request\User;

use App\DTO\Request\WithValidationTrait;

abstract class AbstractUserDTO
{
    use WithValidationTrait;

    public function getFirstName(): ?string
    {
        return null;
    }

    public function getLastName(): ?string
    {
        return null;
    }

    public function getEmail(): ?string
    {
        return null;
    }

    public function getParentId(): ?int
    {
        return null;
    }
}
