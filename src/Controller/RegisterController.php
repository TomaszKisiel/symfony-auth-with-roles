<?php

namespace App\Controller;

use App\Entity\User;
use App\Form\UserType;
use App\Repository\UserRepository;
use Doctrine\ORM\EntityManagerInterface;
use FOS\RestBundle\Controller\AbstractFOSRestController;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\NumberType;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;

class RegisterController extends AbstractFOSRestController {

    private $userRepository;
    private $entityManager;
    private $passwordEncoder;

    public function __construct(
        UserRepository $userRepository,
        EntityManagerInterface $entityManager,
        UserPasswordEncoderInterface $passwordEncoder
    ) {
        $this->userRepository = $userRepository;
        $this->entityManager = $entityManager;
        $this->passwordEncoder = $passwordEncoder;
    }

    /**
     * @Route("/api/register", name="register", methods={"POST"})
     * @param Request $request
     * @return Response
     */
    public function register( Request $request ): Response {
        $user = new User();

        $form = $this->createFormBuilder($user, [ 'csrf_protection' => false ])
            ->add('first_name', TextType::class)
            ->add('last_name', TextType::class)
            ->add('age', NumberType::class)
            ->add('email', EmailType::class)
            ->add('password', PasswordType::class)
            ->getForm();

        $form->submit(
            $request->request->all()
        );

        if ( !$form->isValid() ) {
            return $this->handleView(
                $this->view( $form )
            );
        }

        if ( $form->isSubmitted() ) {
            $user->setPassword(
                $this->passwordEncoder->encodePassword(
                    $user,
                    $form->get('password')->getData()
                )
            );
        }

        $this->entityManager->persist( $form->getData() );
        $this->entityManager->flush();


        return $this->handleView( $this->view( [
            'message' => 'Great! Your profile has been successfully registered and you can access your token via "/api/login" path now.',
            'profile' => $form->getData()
        ], JsonResponse::HTTP_CREATED ) );
    }
}
