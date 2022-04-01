<?php

declare(strict_types=1);

namespace App\EventSubscriber;

use App\Controller\TokenAuthenticatedControllerInterface;
use App\Security\ApiTokenAuthenticator;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\HttpKernel\Event\ControllerEvent;
use Symfony\Component\HttpKernel\KernelEvents;

class TokenSubscriber implements EventSubscriberInterface
{
    /**
     * @var ApiTokenAuthenticator
     */
    private $apiTokenAuthenticator;

    public function __construct(ApiTokenAuthenticator $apiTokenAuthenticator)
    {
        $this->apiTokenAuthenticator = $apiTokenAuthenticator;
    }

    public function onKernelController(ControllerEvent $event)
    {
        $controller = $event->getController();

        if (is_array($controller)) {
            $controller = $controller[0];
        }

        if ($controller instanceof TokenAuthenticatedControllerInterface) {
            $request = $event->getRequest();
            $passport = $this->apiTokenAuthenticator->authenticate($request);
            $event->getRequest()->attributes->set('auth_application', $passport->getUser());
        }
    }

    public static function getSubscribedEvents()
    {
        return [
            KernelEvents::CONTROLLER => 'onKernelController',
        ];
    }
}
