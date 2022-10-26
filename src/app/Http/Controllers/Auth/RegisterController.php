<?php

namespace App\Http\Controllers\Auth;

use App\Http\Resources\UserResource;
use App\Models\User;
use App\Http\Controllers\Controller;
use App\Models\UserDetail;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Exceptions\ApiException;
use Symfony\Component\HttpFoundation\Response as ResponseStatusCode;
use App\Http\Utility\HttpResponse as HttpResponseUtility;

class RegisterController extends Controller
{
    /**
     * Register user
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function register(Request $request): \Illuminate\Http\JsonResponse
    {

        DB::beginTransaction();

        try {

            $fields = $request->validate([
                'username' => 'required|string|unique:users,username|max:50',
                'email' => 'required|string|unique:users,email|max:100',
                'password' => 'required|string|confirmed|max:255',
                'firstname' => 'required|string|max:50',
                'lastname' => 'required|string|max:50',
                'phone_number' => 'required|string|max:40',
                'address' => 'required|string|max:255'
            ]);

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

        } catch (ApiException $apiExce) {
            return (new HttpResponseUtility([], $apiExce->getMessage(), $apiExce->getCode()))->getJsonResponse();
        } catch (\Exception $e) {
            DB::rollBack();
            return (new HttpResponseUtility([], 'An error has ocurred'))->getJsonResponse();
        }
    }
}
