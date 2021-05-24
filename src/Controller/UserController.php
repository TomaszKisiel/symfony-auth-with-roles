<?php

namespace App\Controller;

use App\Entity\User;
use App\Form\UserType;
use App\Repository\UserRepository;
use App\Swagger\User\ForbiddenResponse;
use App\Swagger\User\InvalidResponse;
use App\Swagger\User\Profile;
use App\Swagger\User\ProfileList;
use App\Swagger\User\RequestBody;
use App\Swagger\User\UnauthenticatedResponse;
use Doctrine\ORM\EntityManagerInterface;
use FOS\RestBundle\Controller\AbstractFOSRestController;
use Knp\Component\Pager\PaginatorInterface;
use Nelmio\ApiDocBundle\Annotation\Model;
use Nelmio\ApiDocBundle\Annotation\Security;
use OpenApi\Annotations as OA;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;

/**
 * Class UserController
 * @OA\Tag(name="User")
 * @Security(name="Bearer")
 * @package App\Controller
 */
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
     * @OA\Get(summary="Get users list")
     * @OA\Response (
     *     response=200,
     *     description="Returns paginated list of registered users",
     *     @Model(type=ProfileList::class)
     * ),
     * @OA\Response(
     *     response=401,
     *     description="There is required to provide correct api token to use this resources",
     *     @OA\JsonContent(ref="#/components/schemas/UnauthenticatedResponse")
     * ),
     * @OA\Response(
     *     response=403,
     *     description="Only authenticated admins can use this resources",
     *     @Model(type=ForbiddenResponse::class)
     * ),
     * @param Request $request
     * @return mixed
     */
    public function index( Request $request ) {
        $this->denyAccessUnlessGranted( 'ROLE_ADMIN' );

        return $this->view( [
            "users" => $this->paginator->paginate(
                $this->userRepository->findAllQuery(),
                $request->query->getInt( 'page', 1 ),
                $request->query->getInt( 'per_page', 10 )
            )
        ], JsonResponse::HTTP_OK );
    }

    /**
     * @Route("/api/users/{id}", name="users_show", methods={"GET"})
     * @OA\Get(summary="Get user")
     * @OA\Response (
     *     response=200,
     *     description="Returns selected user data",
     *     @Model(type=Profile::class)
     * ),
     * @OA\Response(
     *     response=401,
     *     description="There is required to provide correct api token to use this resources",
     *     @OA\JsonContent(ref="#/components/schemas/UnauthenticatedResponse")
     * ),
     * @OA\Response(
     *     response=403,
     *     description="Only authenticated admins can use this resources",
     *     @Model(type=ForbiddenResponse::class)
     * ),
     * @param int $id
     * @return \FOS\RestBundle\View\View
     */
    public function show( int $id ) {
        $this->denyAccessUnlessGranted( "ROLE_ADMIN" );

        return $this->view(
            $this->findUserById( $id ),
            JsonResponse::HTTP_OK
        );
    }

    /**
     * @Route("/api/users", name="users_store", methods={"POST"})
     * @OA\Post(summary="Store new user")
     * @OA\RequestBody(
     *     required=true,
     *     @Model(type=RequestBody::class)
     * )
     * @OA\Response (
     *     response=200,
     *     description="New user account has been succesfully created",
     *     @OA\JsonContent(
     *         @OA\Property(property="message", example="Great! New user profile has been successfully created!"),
     *         @OA\Property(
     *             property="user",
     *             type="object",
     *             @OA\Property(property="id", title="id", format="int64", example=1),
     *             @OA\Property(property="email", title="email", format="string", maxLength=160, example="john.doe@example.com"),
     *             @OA\Property(property="roles", title="roles", format="array", example={"ROLE_USER"}),
     *             @OA\Property(property="first_name", title="first_name", format="string", maxLength=64, example="John"),
     *             @OA\Property(property="last_name", title="last_name", format="string", maxLength=64, example="Doe"),
     *             @OA\Property(property="age", title="age", format="int64", example=1),
     *         )
     *     ),
     * ),
     * @OA\Response(
     *     response=400,
     *     description="The given data was invalid.",
     *     @Model(type=InvalidResponse::class)
     * ),
     * @OA\Response(
     *     response=401,
     *     description="There is required to provide correct api token to use this resources",
     *     @Model(type=UnauthenticatedResponse::class)
     * ),
     * @OA\Response(
     *     response=403,
     *     description="Only authenticated admins can use this resources",
     *     @Model(type=ForbiddenResponse::class)
     * ),
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
     * @OA\Patch(summary="Update user")
     * @OA\RequestBody(
     *     @Model(type=RequestBody::class)
     * )
     * @OA\Response (
     *     response=200,
     *     description="User account has been succesfully updated",
     *     @OA\JsonContent(
     *         @OA\Property(property="message", example="Great! User profile has been successfully updated!"),
     *         @OA\Property(
     *             property="user",
     *             type="object",
     *             @OA\Property(property="id", title="id", format="int64", example=1),
     *             @OA\Property(property="email", title="email", format="string", maxLength=160, example="john.doe@example.com"),
     *             @OA\Property(property="roles", title="roles", format="array", example={"ROLE_USER"}),
     *             @OA\Property(property="first_name", title="first_name", format="string", maxLength=64, example="John"),
     *             @OA\Property(property="last_name", title="last_name", format="string", maxLength=64, example="Doe"),
     *             @OA\Property(property="age", title="age", format="int64", example=1),
     *         )
     *     ),
     * ),
     * @OA\Response(
     *     response=400,
     *     description="The given data was invalid.",
     *     @Model(type=InvalidResponse::class)
     * ),
     * @OA\Response(
     *     response=401,
     *     description="There is required to provide correct api token to use this resources",
     *     @Model(type=UnauthenticatedResponse::class)
     * ),
     * @OA\Response(
     *     response=403,
     *     description="Only authenticated admins can use this resources",
     *     @Model(type=ForbiddenResponse::class)
     * ),
     * @param Request $request
     * @param int     $id
     * @return \FOS\RestBundle\View\View|Response|void
     */
    public function patchUpdate( Request $request, int $id ) {
        $this->denyAccessUnlessGranted( "ROLE_ADMIN" );

        $user = $this->findUserById( $id );

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
     * @OA\Delete (summary="Delete user")
     * @OA\Response (
     *     response=200,
     *     description="Delete selected user",
     *     @OA\JsonContent(
     *         @OA\Property(property="message", example="Great! User profile has been successfully deleted!")
     *     )
     * ),
     * @OA\Response(
     *     response=401,
     *     description="There is required to provide correct api token to use this resources",
     *     @Model(type=UnauthenticatedResponse::class)
     * ),
     * @OA\Response(
     *     response=403,
     *     description="Only authenticated admins can use this resources",
     *     @Model(type=ForbiddenResponse::class)
     * ),
     * @param $id
     * @return \FOS\RestBundle\View\View
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
