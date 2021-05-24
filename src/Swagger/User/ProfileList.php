<?php


namespace App\Swagger\User;

use OpenApi\Annotations as OA;
use Nelmio\ApiDocBundle\Annotation\Model;

class ProfileList {

    /**
     * @OA\Property(
     *     title="users",
     *     type="array",
     *     @OA\Items(ref="#/components/schemas/Profile")
     * )
     * @var array
     */
    public $users;

}
