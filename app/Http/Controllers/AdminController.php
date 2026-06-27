<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class AdminController extends Controller
{
    public function addpost()
    {
        return view('admin.add_post');
    }
}
