<?php

declare(strict_types=1);

namespace App\Request;

use App\Entity\Application;
use Symfony\Component\HttpFoundation\Request;

class TokenRequest extends Request
{
    public function getApplication(): Application
    {
        return $this->attributes->get('auth_application');
    }

    public function getAppId(): int
    {
        return $this->getApplication()->getId();
    }
}
