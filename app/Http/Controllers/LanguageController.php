<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\App;
use App\Services\LocaleService;

class LanguageController extends Controller
{
    protected $localeService;

    public function __construct(LocaleService $localeService)
    {
        $this->localeService = $localeService;
    }

    public function setLanguage(Request $request)
    {
        $request->validate([
            'site_language' => 'required|in:en,ar',
        ]);
        session(['current_locale' => $request->site_language]);
        return redirect()->back();
    }

}
