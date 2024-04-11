<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;

class LoginController extends Controller
{
    public function Submit(Request $request)
    {
        $request->validate([
            'phone'=>"required|numeric|min:10"
        ]);
        $user = User::firstOrCreate([
            "phone"=>$request->phone
        ]);
        if (!$user){
            return response()->json([
                'message'=>'Could not a process a user with that phone number'
            ],401);

        }

        $user->notify();
    }
}
