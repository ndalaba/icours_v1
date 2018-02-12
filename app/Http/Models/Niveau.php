<?php namespace App\Http\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class Niveau extends Model {

    public $fillable = ['niveau', 'slug'];

    public function setSlugAttribute($value) {
        $this->attributes['slug'] = Str::slug($this->niveau);
    }

    public function classes() {
        return $this->hasMany('App\Http\Models\Classe');
    }

}
