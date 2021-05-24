<?php

namespace App\Controller;

use App\Form\UserType;
use App\Repository\UserRepository;
use App\Swagger\User\InvalidResponse;
use App\Swagger\User\Profile;
use App\Swagger\User\RequestBody;
use App\Swagger\User\UnauthenticatedResponse;
use Doctrine\ORM\EntityManagerInterface;
use FOS\RestBundle\Controller\AbstractFOSRestController;
use Nelmio\ApiDocBundle\Annotation\Model;
use Nelmio\ApiDocBundle\Annotation\Security;
use OpenApi\Annotations as OA;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;

/**
 * Class ProfileController
 * @OA\Tag(name="Profile")
 * @Security(name="Bearer")
 * @package App\Controller
 */
class ProfileController extends AbstractFOSRestController {

    private $userRepository;
    private $entityManager;

    public function __construct(
        UserRepository $userRepository,
        EntityManagerInterface $entityManager
    ) {
        $this->userRepository = $userRepository;
        $this->entityManager = $entityManager;
    }

    /**
     * @Route("/api/profile", name="profile_show", methods={"GET"})
     * @OA\Get(summary="Get authenticated user profile")
     * @OA\Response (
     *     response=200,
     *     description="Authenticated user profile data.",
     *     @Model(type=Profile::class)
     * ),
     * @OA\Response (
     *     response=401,
     *     description="Unauthenticated.",
     *     @Model(type=UnauthenticatedResponse::class)
     * )
     * @return \FOS\RestBundle\View\View
     */
    public function show() {
        $this->denyAccessUnlessGranted( 'ROLE_USER' );

        return $this->view( [
            $this->userRepository->find(
                $this->getUser()->getId()
            )
        ], JsonResponse::HTTP_OK );
    }

    /**
     * @Route("/api/profile", name="profile_update", methods={"PATCH"})
     * @OA\Patch (summary="Update authenticated user profile")
     * @OA\RequestBody(
     *     required=true,
     *     @Model(type=RequestBody::class)
     * )
     * @OA\Response (
     *     response=200,
     *     description="Authenticated user profile data.",
     *     @Model(type=Profile::class)
     * ),
     * @OA\Response (
     *     response=400,
     *     description="The given data was invalid.",
     *     @Model(type=InvalidResponse::class)
     * )
     * @OA\Response (
     *     response=401,
     *     description="Unauthenticated.",
     *     @Model(type=UnauthenticatedResponse::class)
     * )
     * @param Request                      $request
     * @param UserPasswordEncoderInterface $passwordEncoder
     * @return \FOS\RestBundle\View\View
     */
    public function update(
        Request $request,
        UserPasswordEncoderInterface $passwordEncoder
    ) {
        $this->denyAccessUnlessGranted( 'ROLE_USER' );

        $user = $this->userRepository->find( $this->getUser()->getId() );
        $form = $this->createForm( UserType::class, $user );

        $form->submit(
            $request->request->all(),
            false
        );

        if ( !$form->isValid() ) {
            return $this->view( $form );
        }

        if ( $form->isSubmitted() && $request->request->has( 'password' ) ) {
            $user->setPassword(
                $passwordEncoder->encodePassword(
                    $user,
                    $form->get( 'password' )->getData()
                )
            );
        }

        $this->entityManager->flush();

        return $this->view( [
            'message' => 'Great! Your profile has been successfully updated!',
            'user' => $form->getData()
        ], JsonResponse::HTTP_OK );
    }
}
