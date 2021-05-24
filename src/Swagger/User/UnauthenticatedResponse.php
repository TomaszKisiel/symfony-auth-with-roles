<?php

namespace App\Swagger\User;

use OpenApi\Annotations as OA;

/**
 * @OA\Schema(
 *    title="Unauthenticated response",
 *    description="Make sure that you provide correct access token.",
 *    type="object"
 * )
 */
class UnauthenticatedResponse {

    /**
     * @OA\Property (
     *     title="code",
     *     example=401
     * )
     * @var int
     */
    public $code;

    /**
     * @OA\Property (
     *     title="message",
     *     example="Expired JWT Token."
     * )
     * @var string
     */
    public $message;



}
