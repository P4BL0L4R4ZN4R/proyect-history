<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class UserResource extends JsonResource
{
    public function toArray($request)
    {
        return [
            'id'    => $this->id,
            'name'  => $this->name,
            'email' => $this->email,
            'profile_photo_path' => $this->profile_photo_path,
            // Aquí llegan los roles como array
            'roles' => $this->roles->map(function($role) {
                return [
                    'id' => $role->id,
                    'name' => $role->name,
                ];
            }),
        ];
    }
}