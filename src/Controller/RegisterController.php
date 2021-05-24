<?php

namespace App\Controller;

use App\Form\UserType;
use App\Entity\User;
use App\Repository\UserRepository;
use Doctrine\ORM\EntityManagerInterface;
use FOS\RestBundle\Controller\AbstractFOSRestController;
use Nelmio\ApiDocBundle\Annotation\Model;
use OpenApi\Annotations as OA;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\NumberType;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;
use App\Swagger\Register\CreatedResponse;
use App\Swagger\Register\RequestBody;
use App\Swagger\Register\InvalidResponse;

/**
 * Class RegisterController
 * @OA\Post(summary="Allows register new user account")
 * @OA\RequestBody(
 *     required=true,
 *     @Model(type=RequestBody::class)
 * )
 * @OA\Response (
 *     response=201,
 *     description="New user was successfully created.",
 *     @Model(type=CreatedResponse::class)
 * ),
 * @OA\Response (
 *     response=400,
 *     description="The given data was invalid.",
 *     @Model(type=InvalidResponse::class)
 * )
 * @OA\Tag(name="Authentication")
 * @package App\Controller
 */
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

        $form = $this->createFormBuilder( $user, [ 'csrf_protection' => false ] )
            ->add( 'first_name', TextType::class )
            ->add( 'last_name', TextType::class )
            ->add( 'age', NumberType::class )
            ->add( 'email', EmailType::class )
            ->add( 'password', PasswordType::class )
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
                    $form->get( 'password' )->getData()
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
