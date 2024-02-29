<?php

namespace App\Providers;

use Illuminate\Auth\EloquentUserProvider;

class CustomUserProvider extends EloquentUserProvider
{
    public function validateCredentials($user, array $credentials)
    {
        $plain = $credentials['password'];
        if (!$this->hasher->check($plain, $user->getAuthPassword())) {
            return false;
        }
        if ($user->store_id !== $credentials['store_id']) {
            return false;
        }
        return true;
    }
}
