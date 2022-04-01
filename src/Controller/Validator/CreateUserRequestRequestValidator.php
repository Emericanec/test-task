<?php

declare(strict_types=1);

namespace App\Controller\Validator;

use Symfony\Component\Validator\Constraints as Assert;

class CreateUserRequestRequestValidator extends AbstractRequestValidator
{
    public function rules(): Assert\Collection
    {
        return new Assert\Collection([
            'first_name' => [
                new Assert\NotBlank(),
            ],
            'last_name' => [
                new Assert\NotBlank(),
            ],
            'email' => [
                new Assert\NotBlank(),
                new Assert\Email(),
            ],
        ]);
    }

    public function getData(): array
    {
        return json_decode($this->request->getContent(), true);
    }
}
