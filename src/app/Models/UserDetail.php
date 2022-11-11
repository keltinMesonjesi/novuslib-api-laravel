<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Database\Eloquent\SoftDeletes;

class UserDetail extends Model
{
    use SoftDeletes, HasFactory;

    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'users_details';

    /**
     * All fields inside the $guarded array are not mass-assignable
     * @var array
     */
    protected $guarded = [];

    /**
     * Get the user associated with the detail.
     * @return HasOne
     */
    public function user() : HasOne
    {
        return $this->hasOne(User::class);
    }
}
