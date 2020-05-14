<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth; 
use App\User; //User is name of models
use App\student;

class database extends Controller
{
    public $successStatus = 200;
    public function insert(Request $req)
    {
        $req->validate([
            'name' => 'required|string',
            'email' => 'required|string|unique:users',
            'password' => 'required|min:5|max:12',
            'student_id' => 'required',
            'company' => 'required|string',
             
            //'image'=> 'required|image|mimes:jpeg,png,jpg,gif,svg'
        ]);

        $ins= new User([ 
            //User is name of model and ins to store data
            'name' => $req->name,
            'email' => $req->email,
            'password' =>bcrypt($req->password),
            'student_id' => $req->student_id,
            'company'    =>  $req->company,
            //Get image file
            //'image'=> $req->file('image',
           ]);
      
         $ins-> save();

        // $input = $req->all();  //  getting user data
        // $input['password'] = bcrypt($input['password']); //necessary to bcrypt password
        // $ins = User::create($input);  //creating user entry in db,User is model name
        // //after register user get its own token
        // $success['token'] =  $ins->createToken('MyApp')-> accessToken; 

         return response()->json([
             'status' =>'success',
             //'success' => $success,
            'message' => 'Successfully created user!'
        ], 200);
      }

    public function login() 
    { 
        if(Auth::attempt(['email' => request('email'), 'password' => request('password')])) { //match data with db
           // once matched it will get user detail
        $sel = Auth::User(); // User is name of model
        $success['token'] =  $sel->createToken('MyApp')-> accessToken; 
        return response()->json(['success' => $success], $this-> successStatus); 
        } 
        else
        { 
        return response()->json(['error'=>'Unauthorised'], 401); 
        } 
    }

    public function detail(Request $request) 
    { 
      
    $id =$request->input('id');
    $fetch = User::find($id)->stu; //student is name of model
    //find method is used to fetch whole data by that id
    //$fetch =  User::all(); 

    return response()->json(['response'=>[
    'status' =>'true',
    'message' => 'sucessfully fetched user data' ,
    'biodata'=>$fetch,
              
    ]], 200);
  }
    // return response()->json([$req-> User(), $this-> successStatus]); 
    // } 

    public function logout(Request $request)
    {
        $request->User->token()->revoke();        
        return response()->json([
        'message' => 'Successfully logged out'
        ]);
    }

}
