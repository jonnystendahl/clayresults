<?php

namespace Database\Factories;

use App\Models\User;

/**
 * @extends Factory<User>
 */
class UserFactory extends MemberFactory
{
    protected $model = User::class;
}
