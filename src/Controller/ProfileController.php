<?php

namespace App\Controller;

use App\Form\UserType;
use App\Repository\UserRepository;
use Doctrine\ORM\EntityManagerInterface;
use FOS\RestBundle\Controller\AbstractFOSRestController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;

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
     * @return \FOS\RestBundle\View\View
     */
    public function show() {
        $this->denyAccessUnlessGranted( 'ROLE_USER' );

        return $this->view( [
            $this->userRepository->find(
                $this->getUser()->getId()
            )
        ], JsonResponse::HTTP_CREATED );
    }

    /**
     * @Route("/api/profile", name="profile_update", methods={"PATCH"})
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

        if ( $form->isSubmitted() && $request->request->has('password') ) {
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
