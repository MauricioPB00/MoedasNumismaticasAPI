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

class AuthController extends ApiController
{
    private $em;
    private JWTTokenManagerInterface $jwtManager;

    public function __construct(EntityManagerInterface $em, JWTTokenManagerInterface $jwtManager)
    {
        $this->em = $em;
        $this->jwtManager = $jwtManager;
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
        $user->setPhoto('68c4317b07cb1.png');

        //dd($user);
        $this->em->persist($user);
        //dd($user);
        $this->em->flush();

        return $this->respondWithSuccess(sprintf('User %s successfully created', $user->getUsername()));
    }


    /**
     * @Route("/update", name="update", methods={"PUT"})
     */
    public function update(Request $request, EntityManagerInterface $em, UserPasswordEncoderInterface $encoder, LoggerInterface $logger, JWTTokenManagerInterface $jwtManager): JsonResponse
    {
        // Pega o usuário autenticado pelo JWT
        /** @var User $user */
        // $user = $this->getUser();
        // if (!$user) {
        //     return $this->json(['error' => 'Usuário não autenticado'], 401);
        // }

        $user = $this->getUser();
        if (!$user) {
            $logger->warning('Usuário não autenticado. JWT recebido: ' . $request->headers->get('Authorization'));
            return $this->json(['error' => 'Usuário não autenticado'], 401);
        }

        // Decodifica o JSON enviado pelo front
        $data = json_decode($request->getContent(), true);

        // Atualiza campos permitidos
        if (!empty($data['name'])) $user->setName($data['name']);
        if (!empty($data['email'])) $user->setEmail($data['email']);
        if (!empty($data['cpf'])) $user->setCpf($data['cpf']);
        if (!empty($data['rg'])) $user->setRg($data['rg']);
        if (!empty($data['datNasc'])) $user->setDatNasc($data['datNasc']); // se for string, pode converter para DateTime
        if (!empty($data['city'])) $user->setCity($data['city']);
        if (!empty($data['number'])) $user->setNumber($data['number']);

        // // Atualiza senha
        // if (!empty($data['password'])) {
        //     $user->setPassword($encoder->encodePassword($user, $data['password']));
        // }

        $em->persist($user);
        $em->flush();

        $token = $jwtManager->create($user); // se estiver usando LexikJWTAuthenticationBundle
        return $this->json([
            'message' => 'Dados atualizados com sucesso',
            'token' => $token
        ]);

        return $this->json(['message' => 'Dados atualizados com sucesso']);
    }



    /**
     * @Route("/forgot-password", name="forgot_password", methods={"POST"})
     */
    public function forgotPassword(Request $request, EntityManagerInterface $em, MailerInterface $mailer): JsonResponse
    {

        $email = $request->toArray()['email'] ?? null;
        if (!$email) {
            return $this->json(['error' => 'E-mail é obrigatório'], 400);
        }

        $user = $em->getRepository(User::class)->findOneBy(['email' => $email]);
        if (!$user) {
            return $this->json(['message' => 'Se o e-mail existir, enviaremos instruções.'], 200);
        }

        // Criar token único
        $token = bin2hex(random_bytes(32));
        $expiresAt = new \DateTime('+1 hour');

        // Salvar token no banco
        $reset = new PasswordReset();
        $reset->setEmail($email);
        $reset->setToken($token);
        $reset->setExpiresAt($expiresAt);

        $em->persist($reset);
        $em->flush();

        // Enviar email com o link
        $resetUrl = "http://localhost:4200/reset-password?token=" . $token;

        $emailMessage = (new Email())
            // ->from('no-reply@meusite.com')
            ->from('albumnumismatico@gmail.com')
            ->to($email)
            ->subject('Redefinir sua senha')
            ->text("Clique no link para redefinir sua senha: " . $resetUrl);

        // dump($resetUrl);
        // dump($emailMessage);
        $mailer->send($emailMessage);

        return $this->json(['message' => 'Se o e-mail existir, enviaremos instruções.']);
    }


    /**
     * @Route("/reset-password", name="reset_password", methods={"POST"})
     */
    public function resetPassword(Request $request, EntityManagerInterface $em, UserPasswordEncoderInterface $encoder): JsonResponse
    {
        $data = $request->toArray();
        $token = $data['token'] ?? null;
        $newPassword = $data['password'] ?? null;

        if (!$token || !$newPassword) {
            return $this->json(['error' => 'Token e senha são obrigatórios'], 400);
        }

        $reset = $em->getRepository(PasswordReset::class)->findOneBy(['token' => $token]);

        if (!$reset || $reset->getExpiresAt() < new \DateTime()) {
            return $this->json(['error' => 'Token inválido ou expirado'], 400);
        }

        $user = $em->getRepository(User::class)->findOneBy(['email' => $reset->getEmail()]);
        if (!$user) {
            return $this->json(['error' => 'Usuário não encontrado'], 404);
        }

        // Atualizar senha
        $user->setPassword($encoder->encodePassword($user, $newPassword));
        $em->persist($user);
        $em->remove($reset); // invalida token
        $em->flush();

        return $this->json(['message' => 'Senha redefinida com sucesso']);
    }



    /**
     * @Route("/photo", name="photo", methods={"POST"})
     */
    public function uploadPhoto(Request $request, EntityManagerInterface $em): JsonResponse
    {
        /** @var User $user */
        $user = $this->getUser(); // pegar usuário logado

        $file = $request->files->get('photo');
        if (!$file) {
            return $this->json(['error' => 'Nenhum arquivo enviado'], 400);
        }

        $filename = uniqid() . '.' . $file->guessExtension();
        $file->move($this->getParameter('user_photos_dir'), $filename);

        $user->setPhoto($filename);
        $em->flush();

        return $this->json(['success' => true, 'photo' => $filename]);
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
}
