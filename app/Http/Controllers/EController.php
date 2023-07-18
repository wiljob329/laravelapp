<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class EController extends Controller
{
    public function homepage()
    {
        $name = "Petra";
        return view("homepage", ['name' => $name]);
    }

    public function aboutpage()
    {
        return view('single-post');
    }
}
