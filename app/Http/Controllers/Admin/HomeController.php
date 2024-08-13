<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Hotel;
use Illuminate\Http\Request;

class HomeController extends Controller
{
    public function dashboard()
    {
        $hotels= Hotel::all();
        return view('admin.dashboard', compact('Hotels'));
    }
}
