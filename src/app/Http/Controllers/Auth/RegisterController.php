<?php

namespace App\Http\Controllers\Auth;

use App\Http\Resources\UserResource;
use App\Models\User;
use App\Http\Controllers\Controller;
use App\Models\UserDetail;
use App\Http\Requests\Auth\RegisterRequest;
use Symfony\Component\HttpFoundation\Response as ResponseStatusCode;

use App\Http\Utility\HttpLogicAction as HttpLogicActionUtility;

class RegisterController extends Controller
{
    /**
     * Register user
     * @param RegisterRequest $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function register(RegisterRequest $request): \Illuminate\Http\JsonResponse
    {

        return (new HttpLogicActionUtility(ResponseStatusCode::HTTP_CREATED))->executeActionWithDml(function() use ($request) {
            $fields = $request->validated();

            $user = User::create([
                'username' => $fields['username'],
            'email' => $fields['email'],
            'password' => bcrypt($fields['password'])
            ]);

            UserDetail::create([
                'user_id' => $user->id,
            'firstname' => $fields['firstname'],
            'lastname' => $fields['lastname'],
            'phone_number' => $fields['phone_number'],
            'address' => $fields['address'],
            ]);

            $token = $user->createToken('myapptoken')->plainTextToken;

            return [
                'resource' => new UserResource($user),
                'options' => [
                'token' => $token
                ]
            ];
        });

    }
}
