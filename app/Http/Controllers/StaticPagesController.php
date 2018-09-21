<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class StaticPagesController extends Controller
{
    //
    public function home(){
    	return View('static_pages/home');
    }

    public function help(){
    	return View('static_pages/help');
    }

    public function about(){
    	return View('static_pages/about');
    }
}
