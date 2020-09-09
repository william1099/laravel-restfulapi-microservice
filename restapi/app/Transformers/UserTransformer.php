<?php

namespace App\Transformers;

use App\User;
use League\Fractal\TransformerAbstract;

class UserTransformer extends TransformerAbstract
{
    /**
     * List of resources to automatically include
     *
     * @var array
     */
    protected $defaultIncludes = [
        //
    ];
    
    /**
     * List of resources possible to include
     *
     * @var array
     */
    protected $availableIncludes = [
        //
    ];
    
    const ATTRIBUTE = [
        "user_id" => "id",
        "isVerified" => "verified",
        "isAdmin" => "admin",
        "CreationDate" => "created_at",
        "DeletionData" => "deleted_at",
        "LastChanged" => "updated_at"
    ];
    /**
     * A Fractal transformer.
     *
     * @return array
     */
    public function transform(User $user)
    {
        return [
            "user_id" => (int) $user->id,
            "name" => (string) $user->name,
            "email" => (string) $user->email,
            "gender" => $user->gender,
            "isVerified" => ($user->isVerified()),
            "isAdmin" => ($user->isAdmin()),
            "CreationDate" => $user->created_at,
            "LastChanged" => $user->updated_at,
            "DeletionDate" => $user->deleted_at ?? "null"        
        ];
    }

    public static function getOriginalAttribute($key) {
        if(array_key_exists($key, UserTransformer::ATTRIBUTE)) {
            return UserTransformer::ATTRIBUTE[$key];
        }
        return $key;
    }

}
