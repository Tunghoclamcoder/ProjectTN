<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Routing\Controller;

class ShopController extends Controller
{
    public function index()
    {
        return view('customer.home');
    }
}
