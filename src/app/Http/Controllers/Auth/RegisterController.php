<?php

namespace App\Http\Controllers\Auth;

use App\Http\Resources\UserResource;
use App\Models\User;
use App\Http\Controllers\Controller;
use App\Models\UserDetail;
use App\Http\Requests\Auth\RegisterRequest;
use Illuminate\Support\Facades\DB;
use App\Exceptions\ApiException;
use Illuminate\Validation\ValidationException;
use Symfony\Component\HttpFoundation\Response as ResponseStatusCode;
use App\Http\Utility\HttpResponse as HttpResponseUtility;

class RegisterController extends Controller
{
    /**
     * Register user
     * @param RegisterRequest $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function register(RegisterRequest $request): \Illuminate\Http\JsonResponse
    {

        DB::beginTransaction();

        try {

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

            $responseData = [
                'resource' => new UserResource($user),
                'options' => [
                'token' => $token
                ]
            ];

            DB::commit();
            return (new HttpResponseUtility($responseData, '', ResponseStatusCode::HTTP_CREATED))->getJsonResponse();

        } catch (ApiException $apiExcep) {
            return (new HttpResponseUtility([], $apiExcep->getMessage(), $apiExcep->getCode()))->getJsonResponse();
        } catch (\Exception $e) {
            DB::rollBack();
            return (new HttpResponseUtility([], 'An error has ocurred'))->getJsonResponse();
        }
    }
}
