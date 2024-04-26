<?php

namespace App\Traits;
use Illuminate\Support\Facades\Http;
use GuzzleHttp\Client;
use ClickSend\Configuration;
use ClickSend\Api\SMSApi;
use ClickSend\Model\SmsMessage;
use ClickSend\Model\SmsMessageCollection;

trait ClickSendSms
{
    public function send($to,int $loginCode)
    {
        $response =http::withBasicAuth(
            'louissosthenes9@gmail.com',
            'A39ED6E-DCD0-4506-27F1-C13A29A72ADD'
        )->post('https://rest.clicksend.com/v3/sms/send',[
            'messages'=>[
                'body'=>'Your login code is'. $loginCode .'\n dont share this with anyone!',
                'to'=>$to,
                'from'=>'255697080072'
            ]
        ]);

        return $response;
    }

    public function SendSms($to,int $loginCode){
        $client = new Client();

        $config = Configuration::getDefaultConfiguration()
        ->setUsername('louissosthenes9@gmail.com')
        ->setPassword('A39ED6E-DCD0-4506-27F1-C13A29A72ADD');

        $apiInstance = new SMSApi($client, $config);

        $msg = new SmsMessage();
        $msg->setSource("+255697080072");
        $msg->setBody('Your login code is'. $loginCode .'\n dont share this with anyone!');
        $msg->setTo($to);

        $sms_messages = new SmsMessageCollection();
        $sms_messages->setMessages([$msg]);

        try {
            // Send the SMS
            $result = $apiInstance->smsSendPost($sms_messages);
           return "success";
        } catch (\Exception $e) {
            return 'Exception when calling SMSApi->smsSendPost: '. $e->getMessage();
        }

    }


}
