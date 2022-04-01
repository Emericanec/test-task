<?php

declare(strict_types=1);

namespace App\DTO\Request;

use App\Exception\Validation\InvalidRequestException;
use Symfony\Component\Validator\Validator\ValidatorInterface;

trait WithValidationTrait
{
    /**
     * @var ValidatorInterface
     */
    protected $validator;

    /**
     * @throws InvalidRequestException
     */
    public function validate(): void
    {
        $violations = $this->validator->validate($this);
        if (0 < $violations->count()) {
            $error = $violations->get(0);
            $message = "{$error->getPropertyPath()} {$error->getMessage()}";
            throw new InvalidRequestException($message);
        }
    }
}
