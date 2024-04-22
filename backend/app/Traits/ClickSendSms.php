<?php

namespace App\Traits;
use Illuminate\Support\Facades\Http;

trait ClickSendSms
{
    public function send(String $to,int $loginCode)
    {
        $response =http::withBasicAuth(
            'louissosthenes9@gmail.com',
            'A39ED6E-DCD0-4506-27F1-C13A29A72ADD'
        )->post('https://rest.clicksend.com/v3/sms/send',[
            'messages'=>[
                'body'=>'Your login code is'. $loginCode .'\n dont share this with anyone!',
                'to'=>$to,
                'from'=>'06970000076'
            ]
        ]);

        return $response;
    }
}
