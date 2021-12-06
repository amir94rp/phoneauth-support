<?php

namespace PhoneAuth\Support\Models;

use Illuminate\Auth\Authenticatable;
use PhoneAuth\Support\Traits\MustVerifyNumber;
use PhoneAuth\Support\Contracts\MustVerifyNumber as MustVerifyNumberContract;
use Illuminate\Contracts\Auth\Access\Authorizable as AuthorizableContract;
use Illuminate\Contracts\Auth\Authenticatable as AuthenticatableContract;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Foundation\Auth\Access\Authorizable;

class User extends Model implements
    AuthenticatableContract,
    AuthorizableContract,
    MustVerifyNumberContract
{
    use Authenticatable, Authorizable, MustVerifyNumber;
}
