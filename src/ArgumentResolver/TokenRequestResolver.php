<?php

declare(strict_types=1);

namespace App\ArgumentResolver;

use App\Request\TokenRequest;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Controller\ArgumentValueResolverInterface;
use Symfony\Component\HttpKernel\ControllerMetadata\ArgumentMetadata;

class TokenRequestResolver implements ArgumentValueResolverInterface
{
    public function supports(Request $request, ArgumentMetadata $argument): int
    {
        return $argument->getType() === TokenRequest::class & null !== $request->attributes->get('auth_application');
    }

    public function resolve(Request $request, ArgumentMetadata $argument): iterable
    {
        yield new TokenRequest(
            $request->query->all(),
            $request->request->all(),
            $request->attributes->all(),
            $request->cookies->all(),
            $request->cookies->all(),
            $request->server->all(),
            $request->getContent()
        );
    }
}
