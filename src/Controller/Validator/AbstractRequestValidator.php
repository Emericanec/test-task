<?php

declare(strict_types=1);

namespace App\Controller\Validator;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Component\Validator\ConstraintViolationListInterface;
use Symfony\Component\Validator\Validator\ValidatorInterface;

abstract class AbstractRequestValidator
{
    /**
     * @var ValidatorInterface
     */
    protected $validator;

    /**
     * @var Request
     */
    protected $request;

    public function __construct(Request $request, ValidatorInterface $validator) {
        $this->validator = $validator;
        $this->request = $request;
    }

    abstract public function rules(): Assert\Collection;

    abstract public function getData(): array;

    public function validate(): ConstraintViolationListInterface
    {
        return $this->validator->validate($this->getData(), $this->rules());
    }
}
