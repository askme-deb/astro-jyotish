<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class AstrologerRegistrationController extends Controller
{
    public function showForm()
    {
        return view('astrologers.register');
    }
}
