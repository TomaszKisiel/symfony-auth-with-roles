<?php

namespace App\Swagger\User;

use OpenApi\Annotations as OA;

/**
 * @OA\Schema(
 *    title="Registration invalid response",
 *    description="Registration response when given data was invalid..",
 *    type="object"
 * )
 */
class ForbiddenResponse {

    /**
     * @OA\Property (
     *     title="code",
     *     example=403
     * )
     * @var int
     */
    public $code;

    /**
     * @OA\Property (
     *     title="message",
     *     example="Access denaid. Only authenticated admins can use this resource."
     * )
     * @var string
     */
    public $message;



}
