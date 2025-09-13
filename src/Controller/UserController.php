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
use App\Entity\PasswordReset;
use Symfony\Component\Mime\Email;
use Symfony\Component\Mailer\MailerInterface;
use Psr\Log\LoggerInterface;
use App\Repository\UserRepository;

class UserController extends ApiController
{
    private $em;
    private JWTTokenManagerInterface $jwtManager;

    public function __construct(EntityManagerInterface $em, JWTTokenManagerInterface $jwtManager)
    {
        $this->em = $em;
        $this->jwtManager = $jwtManager;
    }

    /**
     * @Route("/informacao", name="informacao", methods={"GET"})
     */
    public function informacao(): JsonResponse
    {
        /** @var User $user */
        $user = $this->getUser(); // pegar usuário logado

        if (!$user) {
            return $this->json(['error' => 'Usuário não autenticado'], 401);
        }

        return $this->json([
            'id'     => $user->getId(),
            'email'  => $user->getEmail(),
            'name'   => $user->getName(),
            'cpf'    => $user->getCpf(),
            'rg'     => $user->getRg(),
            'city'   => $user->getCity(),
            'number' => $user->getNumber(),
            'photo'  => $user->getPhoto(),
        ]);
    }

    /**
     * @Route("/user", name="user", methods={"GET"})
     */
    public function user(UserRepository $userRepository): JsonResponse
    {
        $users = $userRepository->findAll();

        $data = array_map(function ($user) {
            return [
                'id' => $user->getId(),
                'name' => $user->getName(),
                'email' => $user->getEmail(),
                'photo' => $user->getPhoto(),
            ];
        }, $users);

        return $this->json($data);
    }
}
