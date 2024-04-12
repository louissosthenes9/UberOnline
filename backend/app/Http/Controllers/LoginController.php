<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Notifications\LoginNeedsVerification;
use Illuminate\Http\Request;

class LoginController extends Controller
{
    public function Submit(Request $request)
    {
        $request->validate([
            'phone'=>"required|numeric|min:10"
        ]);
        $user = User::findOrNew([
            "phone"=>$request->phone
        ]);
        if (!$user){
            return response()->json([
                'message'=>'Could not a process a user with that phone number'
            ],401);

        }
        //send one time verification code
        $user->notify(new LoginNeedsVerification());
        return response()->json(['message'=>'Text message notification sent.']);
    }

         public function verify(Request $request){
            //validate incoming request
             $request->validate([
                'phone'=>'required | numeric | min:10',
                 'login_code'=>'required | numeric | between:111111,999999'
             ]);

             //find the user
             $user = User::where('phone',$request->phone)
                 ->where('login_code',$request->logic_code)
                 ->first();

             if($user){
                 $user->update(
                   [  'login_code'=>null]
                 );
                 return $user->createToken($request->logic_code)->plainTextToken;
             }

             return response()->json([
                 'message'=>'invalid verification'
             ]);


             //check code
         }
}
