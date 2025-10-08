<?php

namespace App\EventListener;

use Lexik\Bundle\JWTAuthenticationBundle\Event\AuthenticationSuccessEvent;
use Symfony\Component\HttpFoundation\RequestStack;
use App\Entity\User;



class AuthenticationSuccessListener {
/**
 * @var RequestStack
 */
private $requestStack;

/**
 * @param RequestStack $requestStack
 */
public function __construct(RequestStack $requestStack)
{
    $this->requestStack = $requestStack;
}



/**
 * @param AuthenticationSuccessEvent $event
 */
public function onAuthenticationSuccessResponse(AuthenticationSuccessEvent $event )
{
   $data = $event->getData();
        $user = $event->getUser();
        $request = $this->requestStack->getCurrentRequest();

        if (!is_object($user)) {
            return;
        }

        $data['ip'] = $request->getClientIp();
        $data['id'] = $user->getId();
        $data['name'] = $user->getName();
        $data['email'] = $user->getEmail();
        $data['permi'] = $user->getPermi(); 
        $data['roles'] = $event->getUser()->getRoles();
        $event->setData($data);
}
}