<?php

namespace App\Controller;

use App\Entity\User;
use App\Form\UserProfileType;
use App\Form\UserType;
use App\Repository\UserRepository;
use Doctrine\ORM\EntityManagerInterface;
use FOS\RestBundle\Controller\AbstractFOSRestController;
use FOS\RestBundle\FOSRestBundle;
use Knp\Component\Pager\PaginatorInterface;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;

class UserController extends AbstractFOSRestController {

    private $entityManager;
    private $userRepository;
    private $paginator;
    private $passwordEncoder;

    public function __construct(
        EntityManagerInterface $entityManager,
        UserRepository $userRepository,
        PaginatorInterface $paginator,
        UserPasswordEncoderInterface $passwordEncoder
    ) {
        $this->entityManager = $entityManager;
        $this->userRepository = $userRepository;
        $this->paginator = $paginator;
        $this->passwordEncoder = $passwordEncoder;
    }

    /**
     * @Route("/api/users", name="users_index", methods={"GET"})
     * @param Request $request
     * @return mixed
     */
    public function index( Request $request ) {
        $this->denyAccessUnlessGranted( 'ROLE_ADMIN' );

        return $this->view(
            $this->paginator->paginate(
                $this->userRepository->findAllQuery(),
                $request->query->getInt( 'page', 1 ),
                $request->query->getInt( 'per_page', 10 )
            )
        );
    }

    /**
     * @Route("/api/users/{id}", name="users_show", methods={"GET"})
     * @param int $id
     * @return \FOS\RestBundle\View\View
     */
    public function show( int $id ) {
        $this->denyAccessUnlessGranted( "ROLE_ADMIN" );

        return $this->view(
            $this->findUserById( $id )
        );
    }

    /**
     * @Route("/api/users", name="users_store", methods={"POST"})
     * @param Request $request
     * @return \FOS\RestBundle\View\View
     */
    public function store( Request $request ) {
        $this->denyAccessUnlessGranted( "ROLE_ADMIN" );

        $user = new User();
        $form = $this->createForm( UserType::class, $user );

        $form->submit(
            $request->request->all()
        );

        if ( !$form->isValid() ) {
            return $this->view( $form );
        }

        if ( $form->isSubmitted() && $request->request->has( 'password' ) ) {
            $user->setPassword(
                $this->passwordEncoder->encodePassword(
                    $user,
                    $form->get( 'password' )->getData()
                )
            );
        }

        $this->entityManager->persist( $form->getData() );
        $this->entityManager->flush();

        return $this->view( [
            'message' => 'Great! New user profile has been successfully created!',
            'user' => $form->getData()
        ], JsonResponse::HTTP_CREATED );

    }

    /**
     * @Route("/api/users/{id}", name="users_patch_update", methods={"PATCH"})
     * @param Request $request
     * @param int     $id
     * @return \FOS\RestBundle\View\View|Response|void
     */
    public function patchUpdate( Request $request, int $id ) {
        $this->denyAccessUnlessGranted( "ROLE_ADMIN" );

        $user = $this->findUserById( $id );

        $form = $this->createForm( UserType::class, $user);

        $form->submit(
            $request->request->all(),
            false
        );

        if ( !$form->isValid() ) {
            return $this->view( $form );
        }

        if ( $form->isSubmitted() && $request->request->has( 'password' ) ) {
            $user->setPassword(
                $this->passwordEncoder->encodePassword(
                    $user,
                    $form->get( 'password' )->getData()
                )
            );
        }

        $this->entityManager->flush();

        return $this->view( [
            'message' => 'Great! User(id:' . $id . ') profile has been successfully updated!',
            'user' => $form->getData()
        ], JsonResponse::HTTP_OK );
    }

    /**
     * @Route("/api/users/{id}", name="users_destroy", methods={"DELETE"})
     * @param $id
     */
    public function destroy( $id ) {
        $this->denyAccessUnlessGranted( "ROLE_ADMIN" );

        $user = $this->findUserById( $id );

        $this->entityManager->remove( $user );
        $this->entityManager->flush();

        return $this->view( [
            'message' => 'Great! User(id:' . $id . ') profile has been successfully deleted!'
        ], JsonResponse::HTTP_OK );
    }

    private function findUserById( $id ) {
        $user = $this->userRepository->find( $id );

        if ( $user === null ) {
            throw new NotFoundHttpException();
        }

        return $user;
    }
}
