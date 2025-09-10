<?php


namespace App\Controller;


use App\Entity\User;
use Doctrine\ORM\EntityManagerInterface;
use Lexik\Bundle\JWTAuthenticationBundle\Services\JWTTokenManagerInterface;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;
use Symfony\Component\Security\Core\User\UserInterface;

class AuthController extends ApiController
{
    private $em;

    public function __construct(EntityManagerInterface $em)
    {
        $this->em = $em;
    }

    /**
     * @Route("/register", name="register", methods={"POST"})
     * @param Request $request
     * @param UserPasswordEncoderInterface $encoder
     * @return JsonResponse
     */
    public function register(Request $request, UserPasswordEncoderInterface $encoder): JsonResponse
    {
        $request = $this->transformJsonBody($request);
        $name = $request->get('name');
        $password = $request->get('password');
        $email = $request->get('email');
        $permi = 1;
        $cpf = $request->get('cpf');
        $rg = $request->get('rg');
        $datNasc = $request->get('datNasc');
        $city = $request->get('city');
        $datCad = $request->get('datCad');
        $number = $request->get('number');

        if (empty($name) || empty($password) || empty($email) || empty($number)) {
            return $this->respondValidationError("Invalid Name or Password or Email");
        }

        $user = new User();
        $user->setPassword($encoder->encodePassword($user, $password));
        $user->setEmail($email);
        $user->setPermi($permi);
        $user->setName($name);
        $user->setCpf($cpf);
        $user->setRg($rg);
        $user->setDatNasc($datNasc);
        $user->setCity($city);
        $user->setDatCad(new \DateTime());
        $user->setNumber($number);

        //dd($user);
        $this->em->persist($user);
        //dd($user);
        $this->em->flush();

        return $this->respondWithSuccess(sprintf('User %s successfully created', $user->getUsername()));
    }
}
