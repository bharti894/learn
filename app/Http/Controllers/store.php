<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth; 
use Illuminate\Foundation\Auth\VerifiesEmails;
use Illuminate\Auth\Events\Verified;
use Illuminate\Support\Facades\file;
use Illuminate\Support\Facades\Hash;

use App\student;
use Validator;


class store extends Controller
{
  public $successStatus = 200;
  use VerifiesEmails;


 
  public function insert(Request $req)
  {
    /* @validate required field

    *  if validation failed then return response

    *  else create array and store data 

    * return success response
    */

      $validator = Validator::make($req->all(), [ 
      'fname' =>   'required|alpha',
      'lname' =>   'required|alpha|',
      'fathersname' => 'required',
      'dateofbirth' => 'required|before:today',
      'country' => 'required|alpha',
      'city' => 'required|string',
      'mobileno' => 'required|digits:10', 
      'email'=> 'required|unique:students|email', 
      'password' => 'required|min:6|max:12',  
      'image' => 'required|mimes:jpeg,png,jpg'
      ]);
      
      if ($validator->fails()) { 
      return response()->json([ 'status' =>'failed','error'=>$validator->errors()], 401);            
       }

        //$input = $req->all();  
        //  getting user data
      if($req->hasfile('image'))
      {
      $image = $req->file('image');
      //$destination=public_path().'/images/';
      $namewithextension = $image->getClientOriginalName(); //to get image extension
      $filename =$namewithextension.'.'.time();//get file name
      $image->move(public_path('/images/'), $filename);
      $path = '/images/' . $filename;
      $imurl= url('/'). ''. $path;
      //$images = $filename ;
      }


      $data = new student([     //student is model name
      'fname'        =>   $req->fname,
      'lname'        =>   $req->lname,
      'dateofbirth'  =>   $req->dateofbirth,
      'fathersname'  =>   $req->fathersname,
      'country'      =>   $req->country,
      'city'         =>   $req->city,
      'mobileno'    =>    $req->mobileno,
      'email'       =>    $req->email,
      'password'    =>    bcrypt($req->password),
      'image'       =>    $imurl,
      ]);
        
      $data->save();  
      // When we call the save method, a record will be inserted into the database.
      $data->sendApiEmailVerificationNotification();
        
      $success['token'] =  $data->createToken('MyApp')-> accessToken;
        
      return response()->json(['response'=>[
      'status' =>'true',
      'message' => 'Success!Please confirm yourself by clicking on verify user button sent to you on your email',
      'token' => $success,
      'Details'=>$data,
      
      ]], 200);
        
    }
     

    // Auth::attempt 
    // Auth::guard('student')->attempt

    public function login() 
    { 
          
    if(Auth::attempt(['email' => request('email'), 'password' => request('password')])) { 
    //match data with db 
    //here we have changed default also in auth.php or else we can write in his way
    //Auth::guard('student')->attempt

    // Get the currently authenticated user..
    $sel = Auth::user(); 

    if($sel->email_verified_at !== NULL){
    // user is predefined  method.it validates the user 
    //note:here user starts wuth small letter
    //on sucess it sends token
    $success['token'] =  $sel->createToken('MyApp')-> accessToken; 
    return response()->json(['response'=>[

    'status' =>'true',
    'message' => 'success',
    'email'=> request('email'),
    'acess_token' => $success,
    'token_type' => 'Bearer',    
    ]],
    $this-> successStatus
    );
  } 
    
  else
  {
  return response()->json(['error'=>'Please Verify Email'], 401);
  }
}
  else
  { 
  return response()->json(['error'=>'Unauthorised'], 401); 
  } 
} 

 
  public function detail(Request $request) {
    //fetch user data by id
    //the input method may be applied to retrieve user input:  
    $id =$request->input('id');
    $fetch = student::find($id)->user; //student is name of model
    //find method is used to fetch whole data by that id
    // $fetch =  student::all(); 

    return response()->json(['response'=>[
    'status' =>'true',
    'message' => 'sucessfully fetched user data' ,
    'biodata'=>$fetch,
              
    ]], 200);
  }

  public function update (Request $request) {
    
    $rules = array (
      'id' => 'required|integer',
    );

    $validator = Validator::make($request->all(), $rules);
  
    if ($validator-> fails()){
      return $this->respondValidationError('Fields Validation Failed.', $validator->errors());
    }

    //retrieve single records using find, 
    if($article = student::find($request['id']))
    {
   
    if ($request['fname'] != null) {
      $article->fname = $request['fname'];
    }
    if ($request['email'] != null) {
      $article->email= $request['email'];
    }
    if ($request['lname'] != null) {
      $article->lname = $request['lname'];
    }
    if ($request['city'] != null) {
      $article->city= $request['city'];
    }
    if ($request['dateofbirth'] != null) {
      $article->dateofbirth= $request['dateofbirth'];
    }
    if ($request['mobileno'] != null) {
      $article->mobileno= $request['mobileno'];
    }
    if ($request['country'] != null) {
      $article->country = $request['country'];
    }
    if ($request['fathersname'] != null) {
      $article->fathersname= $request['fathersname'];
    }
    if ($request->hasfile('image')) //hasfile checks  file is already present in the request
    {
    $image = $request->file('image');
    
    $namewithextension = $image->getClientOriginalName(); //to get image extension
    $filename =$namewithextension.'.'.time();//get file name

    $article->image=$filename; //here we set the the filename
    $image->move(public_path() . '/images/', $namewithextension);
    }
    
    $article->save();

    return response()->json(['response'=>[
    'status' =>'true',
    'message' => 'sucessfully updated user' ,
    'data'=> $article,
        
    ]], 200);
    }
    else
    {
    return response()->json([
    'status' =>'failed',
    'message' => 'user id does not exists!',
    ]);
    }
    
}      
   //all method will return all of the results in the model's table
    // $reqdata = $request->all();

    // // here we enter id in url and update data of that user id
    // if($lesson= student::update($request->$reqdata))
    // {
    // $fetch = student::find($id);

  public function destroy(Request $request)
  {
    //retrieve single records using find, 
  if($delt = student::find($request->id))
  { 
  // find method fetch users whole data
  // or $feed=Feed::find(3);  
  // $feed->delete();  
  $lesson= student::where('id',$request->id)->delete($delt);//using id we delete users whole data
   return response()->json([
  'status' =>'true',
  'message' => 'sucessfully deleted user data' ,
  'deleted_data'=> $delt,
  ]);
  }
  else{
  return response()->json([
  'status' =>'false',
  'message' => 'user id does not exists!',
  ]);
  }
  }


  public function details()
  {
    
  if($user = Auth::user())// to fetch user details by token
  {
  return response()->json(['response'=>[
  'status' => 'true',
  'message' => 'success',
  'data' => $user]], $this-> successStatus); 
  }
  else {
  return response()->json([
  'status' => 'false',
  'message' => 'Invalid USER'
  ]);
  }
  }


  public function logout(Request $request)
  {

    if($request->user()->token()->revoke())
    {
    return response()->json([
    'status' => 'true',
    'message' => 'success',
    'response' => 'Successfully logged out'
    ]);
  
    } 
    else {
    return response()->json(['response'=>[
    'status' => 'false',
    'message' => 'User is already logged out'
    ]]);
    
   }
}

/* change password with old password */

 public function changePass(Request $request)
    {
    /*find id from database*/
    $user = student::find($request->id);

    /*if user_id not found*/
    if(!$user)
    {
    return response()->json(['response'=>
    ['status'=>false,
    'message'=>'Id does not exist']]);
    }

    /*
    * Validate all input fields
    */
    $this->validate($request, [
    'old_password' => 'required',
    'new_password' => 'required|min:6|different:password',
    ]);

    /*Check password with exist user password
    * if exist password is right then accept request and update_password
    * else return false
    */
    if (Hash::check($request->old_password, $user->password)) { 
    $user->fill([
    'password' => Hash::make($request->new_password)
    ])->save();

    return response()->json(['response'=>
    ['Status'=>true,
    'message'=>'password updated successfully']]);

     } 
    else 
    {
    return response()->json(['response'=>['status'=>false,"message"=>"Your current password does not matches with the password you provided. Please try again."]]);
    }
  }

  
}
 