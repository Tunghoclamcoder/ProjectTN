<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Routing\Controller;

class CustomerController extends Controller
{
    public function showLoginForm()
    {
        return view('customer.login');
    }

    public function login(Request $request)
    {

    }

    public function logout()
    {

    }

    public function home()
    {
        return view('customer.home');
    }
}
