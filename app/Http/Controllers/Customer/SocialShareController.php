<?php

namespace App\Http\Controllers\Customer;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class SocialShareController extends Controller
{
    public function index()
    {
        $shareButtons = \Share::page(
            '/',
            'Your share text comes here',
        )
        ->facebook()
        ->twitter()
        ->linkedin()
        ->telegram()
        ->whatsapp()        
        ->reddit();
  
        $posts = Post::get();
  
        return view('socialshare', compact('shareButtons', 'posts'));
    }
}
