<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class PhoneVerification extends Model
{
   
    protected $table='phone_verfications';

    protected $fillable = [
        'contact_number','code','status' 
        ];
        
        public function store($request)
        {
       $this->fill($request->all());
       $phone= $this->save();
        return response()->json($phone, 200);
        
      
    }
        public function updateModel($request)
        {
        $this->update($request->all());
        return $this;
        }
}
