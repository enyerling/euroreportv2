<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Hotel;
use Illuminate\Http\Request;

class HomeController extends Controller
{
    protected function redirectTo()
    {
        return '/dashboard';
    }
}
