<?php
namespace App\Services;

use App\Models\User;
use App\Models\UserDetail;
use App\Utility\Helper;

class UserService
{
    /**
     * Create user record together with user detail record
     * @param array $fields
     * @return User $user
     */
    public static function create(array $fields)
    {
        $user = User::create([
            'uid'      => Helper::generateUid(),
            'username' => $fields['username'],
            'email'    => $fields['email'],
            'password' => bcrypt($fields['password'])
        ]);

        UserDetail::create([
            'user_id'      => $user->id,
            'firstname'    => $fields['firstname'],
            'lastname'     => $fields['lastname'],
            'phone_number' => $fields['phone_number'],
            'address'      => $fields['address'],
        ]);

        return $user;
    }
}
