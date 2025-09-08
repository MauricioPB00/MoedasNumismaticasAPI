

symfony new ControleAPI

composer require symfony/orm-pack
composer require --dev symfony/maker-bundle
composer require symfony/security-bundle
composer require "lexik/jwt-authentication-bundle"




na pasta config, criei uma pasta jwt

ai criei private.pem
         public.pem

openssl genrsa -out config/jwt/private.pem -aes256 4096

openssl rsa -pubout -in config/jwt/private.pem -out config/jwt/public.pem

tentei esses 2 comandos mas n funcionou 

ai baixei o ssl

https://slproweb.com/products/Win32OpenSSL.html


entrei na pasta onde ta o ssl ate a bin - abri o cmd como adm 

ai rodei 

openssl genrsa -out private.pem -aes256 4096

openssl rsa -pubout -in private.pem -out public.pem

copiei os 2 e joguei dentro do jwt




Ajustei no .env 

JWT_PASSPHRASE=mauricio

que é a senha criptografada gerada no SSL 







Dentro do Entity 
User.php 


                    <?php

                    namespace App\Entity;

                    use App\Repository\UserRepository;
                    use Doctrine\ORM\Mapping as ORM;
                    use Symfony\Component\Security\Core\User\UserInterface;

                    /**
                    * @ORM\Entity(repositoryClass=UserRepository::class)
                    * @ORM\Table(name="`user`")
                    */
                    class User implements UserInterface
                    {
                        /**
                        * @ORM\Id
                        * @ORM\GeneratedValue
                        * @ORM\Column(type="integer")
                        */
                        private $id;

                        /**
                        * @ORM\Column(type="string", length=255)
                        */
                        private $username;

                        /**
                        * @ORM\Column(type="string", length=255)
                        */
                        private $password;

                        /**
                        * @ORM\Column(type="string", length=255)
                        */
                        private $email;

                        
                        /**
                        * @return string|null
                        */
                        public function getSalt(): ?string
                        {
                            return null;
                        }

                        public function getId(): ?int
                        {
                            return $this->id;
                        }

                        public function getUsername(): ?string
                        {
                            return $this->username;
                        }

                        public function setUsername(string $username): self
                        {
                            $this->username = $username;

                            return $this;
                        }

                        public function getPassword(): ?string
                        {
                            return $this->password;
                        }

                        public function setPassword(string $password): self
                        {
                            $this->password = $password;

                            return $this;
                        }

                        public function getEmail(): ?string
                        {
                            return $this->email;
                        }

                        public function setEmail(string $email): self
                        {
                            $this->email = $email;

                            return $this;
                        }
                        /**
                        * @return array|string[]
                        */
                        public function getRoles(): array
                        {
                            return array('ROLE_USER');
                        }

                        public function eraseCredentials()
                        {
                        }
                    }





Dentro do Controller 

Api Controller.php



                            <?php

                            namespace App\Controller;

                            use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
                            use Symfony\Component\HttpFoundation\JsonResponse;
                            use Symfony\Component\HttpFoundation\Request;

                            class ApiController extends AbstractController
                            {

                                /**
                                * @var integer HTTP status code - 200 by default
                                */
                                protected $statusCode = 200;

                                /**
                                * Gets the value of statusCode.
                                *
                                * @return integer
                                */
                                public function getStatusCode(): int
                                {
                                    return $this->statusCode;
                                }

                                /**
                                * Sets the value of statusCode.
                                *
                                * @param integer $statusCode the status code
                                *
                                * @return self
                                */
                                protected function setStatusCode(int $statusCode): ApiController
                                {
                                    $this->statusCode = $statusCode;

                                    return $this;
                                }

                                /**
                                * Returns a JSON response
                                *
                                * @param array $data
                                * @param array $headers
                                *
                                * @return JsonResponse
                                */
                                public function response(array $data, $headers = []): JsonResponse
                                {
                                    return new JsonResponse($data, $this->getStatusCode(), $headers);
                                }

                                /**
                                * Sets an error message and returns a JSON response
                                *
                                * @param string $errors
                                * @param array $headers
                                * @return JsonResponse
                                */
                                public function respondWithErrors(string $errors, $headers = []): JsonResponse
                                {
                                    $data = [
                                        'status' => $this->getStatusCode(),
                                        'errors' => $errors,
                                    ];

                                    return new JsonResponse($data, $this->getStatusCode(), $headers);
                                }


                                /**
                                * Sets an error message and returns a JSON response
                                *
                                * @param string $success
                                * @param array $headers
                                * @return JsonResponse
                                */
                                public function respondWithSuccess(string $success, $headers = []): JsonResponse
                                {
                                    $data = [
                                        'status' => $this->getStatusCode(),
                                        'success' => $success,
                                    ];

                                    return new JsonResponse($data, $this->getStatusCode(), $headers);
                                }


                                /**
                                * Returns a 401 Unauthorized http response
                                *
                                * @param string $message
                                *
                                * @return JsonResponse
                                */
                                public function respondUnauthorized($message = 'Not authorized!'): JsonResponse
                                {
                                    return $this->setStatusCode(401)->respondWithErrors($message);
                                }

                                /**
                                * Returns a 422 Unprocessable Entity
                                *
                                * @param string $message
                                *
                                * @return JsonResponse
                                */
                                public function respondValidationError($message = 'Validation errors'): JsonResponse
                                {
                                    return $this->setStatusCode(422)->respondWithErrors($message);
                                }

                                /**
                                * Returns a 404 Not Found
                                *
                                * @param string $message
                                *
                                * @return JsonResponse
                                */
                                public function respondNotFound($message = 'Not found!'): JsonResponse
                                {
                                    return $this->setStatusCode(404)->respondWithErrors($message);
                                }

                                /**
                                * Returns a 201 Created
                                *
                                * @param array $data
                                *
                                * @return JsonResponse
                                */
                                public function respondCreated($data = []): JsonResponse
                                {
                                    return $this->setStatusCode(201)->response($data);
                                }


                                protected function transformJsonBody(Request $request): Request
                                {
                                    $data = json_decode($request->getContent(), true);

                                    if ($data === null) {
                                        return $request;
                                    }

                                    $request->request->replace($data);

                                    return $request;
                                }


                            }


            
Dentro do Controller 

AuthController.php



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
                                    * @Route("/api/register", name="register", methods={"POST"})
                                    * @param Request $request
                                    * @param UserPasswordEncoderInterface $encoder
                                    * @return JsonResponse
                                    */
                                    public function register(Request $request, UserPasswordEncoderInterface $encoder): JsonResponse
                                    {
                                        $request = $this->transformJsonBody($request);
                                        $username = $request->get('username');
                                        $password = $request->get('password');
                                        $email = $request->get('email');

                                        if (empty($username) || empty($password) || empty($email)) {
                                            return $this->respondValidationError("Invalid Username or Password or Email");
                                        }


                                        $user = new User($username);
                                        $user->setPassword($encoder->encodePassword($user, $password));
                                        $user->setEmail($email);
                                        $user->setUsername($username);
                                        //dd($user);
                                        $this->em->persist($user);
                                        //dd($user);
                                        $this->em-> flush();
                                        
                                        return $this->respondWithSuccess(sprintf('User %s successfully created', $user->getUsername()));
                                    }

                                    /**
                                    * @Route("/api/login_check", name="login-check", methods={"POST"})
                                    * @param UserInterface $user
                                    * @param JWTTokenManagerInterface $JWTManager
                                    * @return JsonResponse
                                    */
                                    public function getTokenUser(UserInterface $user, JWTTokenManagerInterface $JWTManager): JsonResponse
                                    {
                                        return new JsonResponse(['token' => $JWTManager->create($user)]);
                                    }

                                }


Dentro de CONFIG / PACKEGES / 

Security.yaml 

                                security:
                                    encoders:
                                        App\Entity\User:
                                            algorithm: bcrypt
                                    enable_authenticator_manager: true
                                    # https://symfony.com/doc/current/security.html#registering-the-user-hashing-passwords
                                    password_hashers:
                                        Symfony\Component\Security\Core\User\PasswordAuthenticatedUserInterface: 'auto'
                                    # https://symfony.com/doc/current/security.html#loading-the-user-the-user-provider
                                    #providers:
                                        #users_in_memory: { memory: null }
                                    providers:
                                        app_user_provider:
                                            entity:
                                                class: App\Entity\User
                                                property: username
                                    firewalls:
                                        register:
                                            pattern: ^/api/register
                                            stateless: true
                                            #anonymous: true
                                        login:
                                            pattern:  ^/api/login
                                            stateless: true
                                            #anonymous: true
                                            json_login:
                                                check_path:               /api/login_check
                                                success_handler:          lexik_jwt_authentication.handler.authentication_success
                                                failure_handler:          lexik_jwt_authentication.handler.authentication_failure
                                        api:
                                            pattern:   ^/api
                                            stateless: true
                                            provider: app_user_provider
                                            guard:
                                                authenticators:
                                                    - lexik_jwt_authentication.jwt_token_authenticator
                                        dev:
                                            pattern: ^/(_(profiler|wdt)|css|images|js)/
                                            security: false
                                        main:
                                            #lazy: true
                                            #provider: users_in_memory
                                            #anonymous: true
                                        

                                            # activate different ways to authenticate
                                            # https://symfony.com/doc/current/security.html#the-firewall

                                            # https://symfony.com/doc/current/security/impersonating_user.html
                                            # switch_user: true

                                    # Easy way to control access for large sections of your site
                                    # Note: Only the *first* access control that matches will be used
                                    access_control:
                                        # - { path: ^/admin, roles: ROLE_ADMIN }
                                        # - { path: ^/profile, roles: ROLE_USER }
                                        #- { path: ^/api/register, roles: IS_AUTHENTICATED_ANONYMOUSLY }
                                        #- { path: ^/api/login, roles: IS_AUTHENTICATED_ANONYMOUSLY }
                                        #- { path: ^/api,       roles: IS_AUTHENTICATED_FULLY }

                                when@test:
                                    security:
                                        password_hashers:
                                            # By default, password hashers are resource intensive and take time. This is
                                            # important to generate secure password hashes. In tests however, secure hashes
                                            # are not important, waste resources and increase test times. The following
                                            # reduces the work factor to the lowest possible values.
                                            Symfony\Component\Security\Core\User\PasswordAuthenticatedUserInterface:
                                                algorithm: auto
                                                cost: 4 # Lowest possible value for bcrypt
                                                time_cost: 3 # Lowest possible value for argon
                                                memory_cost: 10 # Lowest possible value for argon




Ai ajustei o Banco

Primeiro criei no PG ADMIN 4 

controle 

ajustei no .env 

                            DATABASE_DRIVER=pgsql
                            DATABASE_HOST=127.0.0.1
                            DATABASE_NAME=controle
                            DATABASE_USER=postgres
                            DATABASE_PASSWORD=123
                            DATABASE_PORT=5432



no doctrine.yaml 

                    doctrine:
                        dbal:
                            dbname: '%env(resolve:DATABASE_NAME)%'
                            user: '%env(resolve:DATABASE_USER)%'
                            password: '%env(resolve:DATABASE_PASSWORD)%'
                            host: '%env(resolve:DATABASE_HOST)%'
                            port: '%env(resolve:DATABASE_PORT)%'
                            driver: '%env(resolve:DATABASE_DRIVER)%'
                            charset: 'utf8'



fiz o migrate 

php bin/console make:migration

php bin/console doctrine:migrations:migrate


symfony server:start



deu erro de cors 


composer require nelmio/cors-bundle

criou o arquivo nelmio_cors.yalm 

ajustei assim 


                    nelmio_cors:
                        defaults:
                            origin_regex: true
                            #allow_origin: ['%env(CORS_ALLOW_ORIGIN)%']
                            allow_origin: ['*']
                            allow_methods: ['GET', 'OPTIONS', 'POST', 'PUT', 'PATCH', 'DELETE']
                            allow_headers: ['Content-Type', 'Authorization']
                            expose_headers: ['Link']
                            max_age: 3600
                        paths:
                            '^/': null


liquei o servidor de novo 

e no postman 

http://127.0.0.1:8000/api/login_check


{
    "username": "mauricio",
    "password": "mauricio"
}
        



http://127.0.0.1:8000/api/register

{
    "username": "mauricio",
    "password": "mauricio",
    "email": "mauricio@gmail.com"
}







https://github.com/nelmio/NelmioCorsBundle
https://helmi-bejaoui.medium.com/a-beginners-guide-on-jwt-authentication-symfony-5-api-based-bd6622bfe975










Para confirmar que as tabelas estao relacionadas


SELECT udt.id AS user_date_time_id, udt.date AS user_date, udt.time AS user_time, u.id AS user_id, u.username AS username
FROM user_date_time udt
JOIN "user" u ON udt.user_id = u.id;




ajustar caminho da foto cadastro no service.yaml
linha 7
parameters:
    upload_dir: 'C:\Users\mau_p\Documents\ControleDeEstoque\routing-controleEstoque\src\img'