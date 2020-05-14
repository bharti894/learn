<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Providers\RouteServiceProvider;
use Illuminate\Foundation\Auth\VerifiesEmails;
use App\student;
use Illuminate\Auth\Events\Verified;


class VerificationController extends Controller
{
    /*
    |--------------------------------------------------------------------------
    | Email Verification Controller
    |----------------------------------------------------s----------------------
    |
    | This controller is responsible for handling email verification for any
    | user that recently registered with the application. Emails may also
    | be re-sent if the user didn't receive the original email message.
    |
    */

    use VerifiesEmails;

    /**
     * Where to redirect users after verification.
     *
     * @var string
     */
    protected $redirectTo = RouteServiceProvider::HOME;

    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('auth');
        $this->middleware('signed')->only('verify');
        $this->middleware('throttle:6,1')->only('verify', 'resend');
    }
    public function show()
    {

    }

    public function verify(Request $request) {
      if($user = Auth::user())
      {
    $userID = $request['id'];
    $user = student::findOrFail($userID);
    $date = date('Y-m-d');
    $user->email_verified_at = $date; // to enable the “email_verified_at field of that user be a current time stamp by mimicing the must verify email feature
    $user->save();
    
    return response()->json('Email verified!');
      }
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
