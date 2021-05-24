<?php

namespace App\Swagger\Register;

use OpenApi\Annotations as OA;

/**
 * @OA\Schema(
 *    title="Registration created response",
 *    description="Registration response after user being created.",
 *    type="object"
 * )
 */
class CreatedResponse {

    /**
     * @OA\Property (
     *     title="message",
     *     example="Great! Your profile has been successfully registered."
     * )
     * @var string
     */
    public $message;


    /**
     * @OA\Property(
     *     title="profile",
     *     type="object",
     *     @OA\Property(property="id", title="id", format="int64", example=1),
     *     @OA\Property(property="email", title="email", format="string", maxLength=160, example="john.doe@example.com"),
     *     @OA\Property(property="roles", title="roles", format="array", example={"ROLE_USER"}),
     *     @OA\Property(property="first_name", title="first_name", format="string", maxLength=64, example="John"),
     *     @OA\Property(property="last_name", title="last_name", format="string", maxLength=64, example="Doe"),
     *     @OA\Property(property="age", title="age", format="int64", example=1),
     * )
     * @var object
     */
    public $profile;

}


