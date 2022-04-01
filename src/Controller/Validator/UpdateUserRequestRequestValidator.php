<?php

declare(strict_types=1);

namespace App\Controller\Validator;

use Symfony\Component\Validator\Constraints as Assert;

class UpdateUserRequestRequestValidator extends AbstractRequestValidator
{
    public function rules(): Assert\Collection
    {
        return new Assert\Collection([
            'email' => [
                new Assert\Email(),
            ],
        ]);
    }

    public function getData(): array
    {
        return json_decode($this->request->getContent(), true);
    }
}
