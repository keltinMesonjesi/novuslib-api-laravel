<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class UserResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param \Illuminate\Http\Request $request
     * @return array|\Illuminate\Contracts\Support\Arrayable|\JsonSerializable
     */
    public function toArray($request): array|\JsonSerializable|\Illuminate\Contracts\Support\Arrayable
    {
        return [
            'type' => 'user',
            'id' => encrypt($this->id),
            'uid' => $this->uid,
            'attributes' => [
                'username' => $this->username,
                'email' => $this->email,
                'detail' => new UserDetailResource($this->userDetail),
            ]
        ];
    }
}
