<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Notifications\LoginNeedsVerification;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Notification;
use App\Traits\ClickSendSms;
use Twilio\Rest\Client;

class LoginController extends Controller
{


    public function Submit(Request $request)
    {
        $request->validate([
            'phone'=>"required|regex:/^\d{6,14}$/"
        ]);
        $user = User::firstOrCreate([
            "phone"=>$request->phone
        ]);
        if (!$user){
            return response()->json([
                'message'=>'Could not a process a user with that phone number'
            ],401);

        }
        $loginCode = rand(111111,999999);
        $user->update(
            ['login_code'=>$loginCode]
        );

        $receiverNumber = $request->phone;
        $message = "Your login code is $loginCode \n dont share this with anyone! "; // Replace with your desired message

        $sid = env('TWILIO_SID');
        $token = env('TWILIO_TOKEN');
        $fromNumber = env('TWILIO_FROM');

        try {
            $client = new Client($sid, $token);
            $client->messages->create($receiverNumber, [
                'from' => $fromNumber,
                'body' => $message
            ]);

            return 'SMS Sent Successfully.';
        } catch (Exception $e) {
            return 'Error: ' . $e->getMessage();
        }
    }

        // try {
        //     Notification::sendNow($user->phone,new LoginNeedsVerification());

        // } catch (Exception $e) {
        //     Log::info('error'.$e);
        // } finally {
        //     return response()->json(['message'=>'Text message notification sent.']);
        // }



   

    public function verify(Request $request){
        //validate incoming request
        $request->validate([
            'phone'=>'required | numeric |regex:/^\d{6,14}$/',
            'login_code'=>'required | numeric | between:111111,999999'
        ]);

        //find the user
        $id = DB::table('users')
                    ->where('phone',$request->phone)
                    ->where('login_code',$request->login_code)
                    ->value('id');

        $user = User::find($id);
        if($user){
            $user->update(
                [  'login_code'=>null]
            );

            $token = $user->createToken($request->login_code)->plainTextToken;
            return $token;
        }

        return response()->json([
            'message'=>'unsuccessful login please try again'
        ]);


        //check code
    }
}
