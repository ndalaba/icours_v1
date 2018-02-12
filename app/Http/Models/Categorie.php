<?php namespace App\Http\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class Categorie extends Model {

    public $fillable = ['categorie','slug','description'];

    public function articles() {
        $this->hasMany('App\Http\Models\Article');
    }

    public function setSlugAttribute($value) {
        if (empty($value))
            $this->attributes['slug'] =Str::slug($this->categorie);
        else
            $this->attributes['slug'] = Str::slug($value);
    }

}
