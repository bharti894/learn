<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Twilio\Jwt\ClientToken;
use GuzzleHttp\Exception\GuzzleException;
use GuzzleHttp\Client;

class Phonecontroller extends Controller
{
protected $code, $phoneVerification;

function __construct()
{
$this->phoneVerification = new \App\PhoneVerification(); // model name PhoneVerification

}


public function store(Request $request)
{
    
$code = rand(1000, 9999); //generate random code
$request['code'] = $code; //add code in $request body

try{
$this->phoneVerification->store($request); //call store method of model
return $this->sendSms($request); // send and return its response
}
 
catch (\Exception $e)
{
echo $e->getcode().' :'.$e->getMessage();
}
}//

public function verifyContact(Request $request)
{
$phoneVerification =$this->phoneVerification::where('contact_number','=',$request->contact_number)
 ->latest() //show the latest if there are multiple
 ->first();
 
if($request->code == $phoneVerification->code)
{

$request["status"] = 'verified';
$phoneVerification->updateModel($request);
$msg["message"] = "verified";
return response()->json([ 
'status' =>'True',
'response' => $msg,
]);
    //'response'
}
else
{
$msg["message"] = "Not verified";
return response()->json([ 
    'status' =>'False',
    'response' =>$msg,
    ]);

} 
}


public function sendSms($request)
{
    
$accountSid = config('app.twilio')['TWILIO_ACCOUNT_SID'];
$authToken = config('app.twilio')['TWILIO_AUTH_TOKEN'];
try
{
$client = new Client(['auth' => [$accountSid, $authToken]]);
$result = $client->post('https://api.twilio.com/2010-04-01/Accounts/'.$accountSid.'/Messages.json',
[
'form_params' => [

'Body' => 'CODE: '. $request->code, //set message body
'To' => $request->contact_number,
'From' => '+12058098530' //we get this number from twilio

]]);
return $result;
}
catch (\Exception $e)
{
    echo $e->getMessage();
}
 }



}