<?php

namespace Platform\Landing\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class LandingController extends Controller
{
    public function __invoke()
    {
        return view('landing::welcome');
    }
}
