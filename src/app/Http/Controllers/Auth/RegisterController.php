<?php

namespace App\Http\Controllers\Auth;

use App\Http\Resources\UserResource;
use App\Http\Controllers\Controller;
use App\Http\Requests\Auth\RegisterRequest;
use Symfony\Component\HttpFoundation\Response as ResponseStatusCode;

use App\Utility\HttpLogicAction as HttpLogicActionUtility;
use App\Services\UserService;

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

            $user = UserService::create($fields);

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
