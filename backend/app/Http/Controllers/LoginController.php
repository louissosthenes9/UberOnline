<?php

namespace App\Http\Controllers;
use Illuminate\Support\Facades\Http;
use App\Models\User;
use App\Notifications\LoginNeedsVerification;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Notification;

class LoginController extends Controller
{]
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
        try{

            $response =Http::acceptJson()->withHeaders([
                'Authorization' => 'Basic cHJvZGlneV90ZWNoMTAxOmdpdEBnaXRodWIuY29tOlVEU00tQ1MyMzQvbGFiLTAtZ2V0dGluZy1zdGFydGVkLWxvdWlzc29zdGhlbmVzOS5naXQ=',
                'Content-Type' => 'application/json',
                'Accept' => 'application/json'
            ])->post('https://messaging-service.co.tz/api/sms/v1/test/text/single', data: [
                "from"=> "N-SMS",
                "to"=>$request->phone,
                "text"=>"Your message",
                "reference"=>"aswqetgcv"
            ]);

            Notification::sendNow($user,new LoginNeedsVerification());
            return $response;

        } catch (Exception $e) {
            Log::info('error'.$e);
        } finally {
            return response()->json(['message'=>'Text message notification sent.']);
        }


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
