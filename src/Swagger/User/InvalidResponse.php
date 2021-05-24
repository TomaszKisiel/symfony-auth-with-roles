<?php

namespace App\Swagger\User;

use OpenApi\Annotations as OA;

/**
 * @OA\Schema(
 *    title="User update invalid response",
 *    description="Update response when given data was invalid..",
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
     *         @OA\Property(property="roles",example={}),
     *         @OA\Property(property="age",example={}),
     *     ),
     * )
     * @var object
     */
    public $errors;

}
