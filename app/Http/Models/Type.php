<?php namespace App\Http\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class Type extends Model {

    public $fillable = ['type','description','slug'];

    public function etudes(){
        $this->hasMany('App\Http\Models\Etude');
    }
    public function setSlugAttribute($value) {
            $this->attributes['slug'] = Str::slug($this->type);
    }


}
