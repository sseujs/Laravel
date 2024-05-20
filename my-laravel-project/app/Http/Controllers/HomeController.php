<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class HomeController extends Controller
{
    public function index()
    {
        return view('home');
    }
    public function blog()
    {
        return view('blog');
    }
    public function learn()
    {
        return view('learn');
    }
}
