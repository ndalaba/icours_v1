<?php namespace App\Http\Models;

use Illuminate\Database\Eloquent\Model;

class Examen extends Model {

    public $fillable = ['examen', 'description'];

    public function corriges() {
      return  $this->belongsToMany('App\Http\Models\Corrige');
    }


}
