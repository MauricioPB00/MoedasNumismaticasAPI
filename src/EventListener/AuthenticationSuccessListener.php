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
    $request = $this->requestStack->getCurrentRequest();

    $data['ip'] = $request->getClientIp();
    $data['permi'] = $event->getUser()->getPermi();
    $data['username'] = $event->getUser()->getUsername();
    $data['id'] = $event->getUser()->getId();
    $data['nome'] = $event->getUser()->getName();

 
    $event->setData($data);
}
}