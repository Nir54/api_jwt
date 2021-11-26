<?php

namespace App\Controller;

use App\Entity\User;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Serializer\SerializerInterface;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Validator\Validator\ValidatorInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Serializer\Exception\NotEncodableValueException;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;
use Lexik\Bundle\JWTAuthenticationBundle\Services\JWTTokenManagerInterface;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface;

class UserController extends AbstractController
{


    public function __construct(TokenStorageInterface $tokenStorageInterface, JWTTokenManagerInterface $jwtManager, EntityManagerInterface $em)
    {
        $this->jwtManager = $jwtManager;
        $this->tokenStorageInterface = $tokenStorageInterface;
        $this->em = $em;
    }

    /**
     * @Route("/", name="default")
     */
    public function default(): Response
    {
        return $this->render('user/index.html.twig', [
            'controller_name' => 'UserController',
        ]);
    }

    /**
     * Ajout utilisateur
     * 
     * @Route("/register", name="register", methods="POST")
     *
     * @param Request $request
     * @param SerializerInterface $serializer
     * @param ValidatorInterface $validator
     * @param UserPasswordEncoderInterface $encoder
     * @return JsonResponse
     */
    public function register(Request $request, SerializerInterface $serializer, ValidatorInterface $validator, UserPasswordEncoderInterface $encoder): JsonResponse
    {
        try {
            
            $users_post = $request->getContent();
            $users = $serializer->deserialize($users_post, User::class, 'json');
            $users->setCreatedAt(new \DateTimeImmutable('NOW', new \DateTimeZone('Africa/Nairobi')));
            $users->setPassword($encoder->encodePassword($users, $users->getPassword()));
            $errors = $validator->validate($users);

            if (count($errors) > 0) {
                return $this->json([
                    "error" => true,
                    "message" => substr((string) $errors, 41, -45)
                ],400);
            }

            $this->em->persist($users);
            $this->em->flush();
            $acces_token = $this->jwtManager->create($users);
            $token_decode = json_decode(base64_decode(str_replace('_', '/', str_replace('-','+',explode('.', $acces_token)[1]))));
            
            return $this->json([
                'error' => false,
                'message' => "L'utilisateur a bien été créé avec succèss",
                "tokens" => [
                    "token" => $acces_token,
                    "refresh-token" => "",
                    "createdAt" => date("Y-m-d H:i:s", $token_decode->iat)
                ]
            ],201);

        } catch (NotEncodableValueException $e) {
            return $this->json([
                "status" => 400,
                "message" => $e->getMessage(),
            ], 400);
        }
    }

    /**
     * @param UserInterface $user
     * @param JWTTokenManagerInterface $JWTManager
     * @return JsonResponse
    */
    public function getTokenUser(UserInterface $user, JWTTokenManagerInterface $JWTManager)
    {
        return new JsonResponse($JWTManager->create($user));
    }


    /**
     * Authenification utilisateur
     * 
     * @Route("/login", name="login", methods="POST")
     *
     * @return JsonResponse
     */
    public function login(): JsonResponse
    {
        $user = $this->getUser();

        return $this->json([
            'email' => $user->getEmail(),
            'roles' => $user->getRoles()
        ]);

        return $this->json([
            'error' => false,
            'message' => "L'utilisateur a bien été créé avec succèss",
            "tokens" => [
                "token" => '$acces_token',
                "refresh-token" => "",
                "createdAt" => 'date("Y-m-d H:i:s", $token_decode->iat)'
            ]
        ],200);
    }
}
