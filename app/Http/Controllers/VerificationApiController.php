<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\student;
use Illuminate\Support\Facades\Auth; 

use Illuminate\Foundation\Auth\VerifiesEmails;
use Illuminate\Auth\Events\Verified;


class VerificationApiController extends Controller
{
    use VerifiesEmails;
   

    public function verify(Request $request) {
      
     
      $userID = $request['id'];
      $user = student::findOrFail($userID);
      $date = date('Y-m-d g:i:s');
      $user->email_verified_at = $date; // to enable the “email_verified_at field of that user be a current time stamp by mimicing the must verify email feature
      $user->save();
      return response()->json('Email verified!');
    
    }

    public function resend(Request $request)
    {
      if ($request->user()->hasVerifiedEmail())
      {
      return response()->json('User already have verified email!', 422);
      // return redirect($this->redirectPath());
      }
      $request->user()->sendEmailVerificationNotification();
      return response()->json('The notification has been resubmitted');
      // return back()->with(‘resent’, true);
    }
}
