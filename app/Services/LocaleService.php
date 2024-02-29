<?php

namespace App\Services;

use Illuminate\Support\Facades\App;

class LocaleService
{
    public function setLocale($language)
    {
        // You can perform additional logic or validation here if needed
        App::setLocale($language);
    }

    public function getCurrentLocale()
    {
        return App::getLocale();
    }
}
