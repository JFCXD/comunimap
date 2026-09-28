<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;


class Controllerinicio extends Controller
{
    public function metodoinicio()
    {
        return view('inicio.index');
    }
}
