<?php namespace App\Http\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class Matiere extends Model {

    public $fillable = ['matiere', 'description', 'slug'];

    public function setSlugAttribute($value) {
        if (empty($value))
            $this->attributes['slug'] = Str::slug($this->matiere);
        else
            $this->attributes['slug'] = Str::slug($value);
    }

    public function corriges() {
        return $this->hasMany('App\Http\Models\Corrige');
    }

    public function cours() {
        return $this->hasMany('App\Http\Models\Cours');
    }

}
