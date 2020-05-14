<?php

namespace App\Http\Controllers;
//namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Carbon\Carbon;
use App\Notifications\PasswordResetRequest;
use App\Notifications\PasswordResetSuccess;
use App\student;
use App\PasswordReset;

class PasswordResetController extends Controller
{
    
    public function create(Request $request)
    {
        
        $request->validate([
        'email' => 'required|string|email',
        ]); 

       $user = student::where('email', $request->email)->first(); //user is any variable    
       // here user enters email and validate  it if it is there
       if (!$user)
        return response()->json([
        'message' => 'We cant find a user with that e-mail address.'
        ], 404);       
        //on sucess it creates password
        $passwordReset = PasswordReset::updateOrCreate([
         'email' => $user->email], // on send emial id
            [
                'email' => $user->email, //email and token will store in db
                'token' => Str::random(60),
             ]
        );      
          if ($user && $passwordReset)
            $user->notify(
                new PasswordResetRequest($passwordReset->token)
            );      
              return response()->json([
             'status' => 'sucess',
            'message' => 'We have e-mailed your password reset link!'
        ]);
    }
    public function find($token)
    {
        $passwordReset = PasswordReset::where('token', $token)->first();
        if (!$passwordReset)
        return response()->json([
       'message' => 'This password reset token is invalid.'
        ], 404);      
        if (Carbon::parse($passwordReset->updated_at)->addMinutes(720)->isPast()) {
        $passwordReset->delete();
        return response()->json([
        'message' => 'This password reset token is invalid.'
        ], 404);
    }        
        return response()->json($passwordReset);
    }


    public function reset(Request $request)
    {
        $request->validate([
        'email' => 'required|string|email',
        'password' => 'required|string|confirmed',
        'token' => 'required|string'
    ]);        
        $passwordReset = PasswordReset::where([
        ['token', $request->token],
        ['email', $request->email]
        ])->first();        

        if (!$passwordReset)
        return response()->json([
        'message' => 'This password reset token is invalid.'
        ], 404);     

        $user =student::where('email', $passwordReset->email)->first();       
         if (!$user)
        return response()->json([
        'message' => 'We cant find a user with that e-mail address.',
            ], 404);  
        $user->password = bcrypt($request->password);
        $user->save();      
        $passwordReset->delete();       
        $user->notify(new PasswordResetSuccess($passwordReset));       
        return response()->json($user);
          
    
}}
