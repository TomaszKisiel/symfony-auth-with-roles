<?php


namespace App\Swagger\User;

use OpenApi\Annotations as OA;

class Profile {

    /** @OA\Property(
     *     property="id",
     *     title="id",
     *     format="int64",
     *     example=1
     * )
     * @var int
     */
    public $id;

    /** @OA\Property(
     *     property="email",
     *     title="email",
     *     format="string",
     *     maxLength=160,
     *     example="john.doe@example.com"
     * )
     * @var string
     */
    public $email;

    /** @OA\Property(
     *     property="roles",
     *     title="roles",
     *     format="array",
     *     example={"ROLE_USER"}
     * )
     * @var string[]
     */
    public $roles;

    /** @OA\Property(
     *     property="first_name",
     *     title="first_name",
     *     format="string",
     *     maxLength=64,
     *     example="John"
     * )
     * @var string
     */
    public $first_name;

    /** @OA\Property(
     *     property="last_name",
     *     title="last_name",
     *     format="string",
     *     maxLength=64,
     *     example="Doe"
     * )
     * @var string
     */
    public $last_name;

    /** @OA\Property(
     *     property="age",
     *     title="age",
     *     format="int64",
     *     example=1
     * )
     * @var string
     */
    public $age;

}
