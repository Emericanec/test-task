<?php

declare(strict_types=1);

namespace App\Controller;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Security\Core\User\UserInterface;

interface TokenAuthenticatedControllerInterface
{
    public function getAuthApplication(Request $request): ?UserInterface;
}
