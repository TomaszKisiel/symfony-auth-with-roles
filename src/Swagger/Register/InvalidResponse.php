<?php

namespace App\Swagger\Register;

use OpenApi\Annotations as OA;

/**
 * @OA\Schema(
 *    title="Registration invalid response",
 *    description="Registration response when given data was invalid..",
 *    type="object"
 * )
 */
class InvalidResponse {

    /**
     * @OA\Property (
     *     title="code",
     *     example=400
     * )
     * @var int
     */
    public $code;

    /**
     * @OA\Property (
     *     title="message",
     *     example="The given data was invalid."
     * )
     * @var string
     */
    public $message;


    /**
     * @OA\Property(
     *     title="profile",
     *     type="object",
     *     @OA\Property(
     *         property="children",
     *         @OA\Property(property="first_name",example={}),
     *         @OA\Property(property="last_name",example={}),
     *         @OA\Property(
     *             property="email",
     *             @OA\Property(
     *                 property="errors",
     *                 example={"This value is already used."}
     *             )
     *         ),
     *         @OA\Property(
     *             property="password",
     *             @OA\Property(
     *                 property="errors",
     *                 example={"This value should not be blank."}
     *             )
     *         ),
     *         @OA\Property(property="age",example={}),
     *     ),
     * )
     * @var object
     */
    public $errors;

}
