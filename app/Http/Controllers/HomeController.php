<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\Artisan;
use Illuminate\View\View;
use Auth;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;

class HomeController extends Controller
{
    public function index(): View
    {
        if (!isset(Auth::user()->id)) 
        {
            return view('home');
        }
        else
        {
            if (!isset(Auth::user()->id) AND Auth::user()->id == NULL)
            {
                return view('home');
            }
            else
            {
                return view('pages.dashboard');
            }
        }
    }
    public function registerpro(): View
    {
         return view('pages.registerpro');
    }

    public function clearCache(): View
    {
        Artisan::call('cache:clear');

        return view('clear-cache');
    }
}
