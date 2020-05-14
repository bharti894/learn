<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/

// Route::middleware('auth:api')->get('/user', function (Request $request) {
//     return $request->user();
// });

Route::post('/ins','database@insert');  //to insert datta
Route::post('login2','database@login');      //login
Route::get('det','database@detail'); 

// Route::group(['middleware' => 'auth:api'], function()
// {  
// Route::get('detail','database@detail');      // to get details of user
// });


// laravel CRUD TASK API'S
Route::post('/signup','store@insert');
Route::post('/login','store@login');
Route::get('/fetch','store@detail');
Route::post('/update','store@update');
Route::post('/del','store@destroy');
Route::group(['middleware' => 'auth:api'], function()
{  
Route::get('details','store@details'); 
Route::get('logout','store@logout');      

});
   

/* Forgot Password */
Route::group([    
'middleware' => 'api',    
'prefix' => 'password'
], function () {    
Route::post('create', 'PasswordResetController@create');
Route::get('/find/{token}', 'PasswordResetController@find');
Route::post('reset', 'PasswordResetController@reset');
});

/* phone verification */
Route::post('sendsms','PhoneController@store');
Route::post('verifyuser','PhoneController@verifyContact');
/* email verification */
Route::get('email/verify/{id}','VerificationApiController@verify')->name('verificationapi.verify');
Route::get('email/resend', 'VerificationApiController@resend')->name('verificationapi.resend');
/* change password using old */
Route::post('change', 'store@changePass');