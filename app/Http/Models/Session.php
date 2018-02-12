<?php namespace App\Http\Models;

use Illuminate\Database\Eloquent\Model;

class Session extends Model {

    public $fillable = ['session'];

   public function corriges(){
       return $this->belongsToMany('App\Http\Models\Corrige');
   }

}
