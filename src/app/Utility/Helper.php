<?php

namespace App\Utility;

use Illuminate\Support\Str;

class Helper
{
    public static function generateUid()
    {
        return strtoupper(str_replace('-', '', Str::uuid()));
    }
}
