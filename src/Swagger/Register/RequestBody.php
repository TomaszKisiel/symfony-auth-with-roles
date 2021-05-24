<?php


namespace App\Swagger\Register;

use OpenApi\Annotations as OA;

/**
 * Class User
 * @package App\Swagger\Register
 * @OA\Schema(
 *     title="Registration request body"
 * )
 */
class RequestBody {

    /**
     * @OA\Property(
     *      title="email",
     *      example="john.doe@example.com"
     * )
     * @var string
     */
    public $email;

    /**
     * @OA\Property (
     *     title="password",
     *     example="secret"
     * )
     * @var string
     */
    public $password;

    /**
     * @OA\Property(
     *     title="first_name",
     *     maxLength = 64,
     *     example = "John"
     * )
     * @var string
     */
    public $first_name;

    /**
     * @OA\Property(
     *     title="last_name",
     *     maxLength = 64,
     *     example = "Doe"
     * )
     * @var string
     */
    public $last_name;

    /**
     * @OA\Property(
     *     title="age",
     *     example = 34
     * )
     * @var int
     */
    public $age;


}
