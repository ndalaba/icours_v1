<?php namespace App\Http\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class Article extends Model {


    public $fillable = ['categorie_id', 'titre', 'slug', 'contenu', 'image', 'publie', 'extrait', 'etat'];

    public static $rules = ['titre' => 'required', 'contenu' => 'required'];

    public function setSlugAttribute($value) {
       /* if (empty($value))
            $this->attributes['slug'] = Str::slug($this->titre);
        else
            $this->attributes['slug'] = Str::slug($value);*/
        $this->attributes['slug'] = Str::slug($this->titre);
    }

    public function setExtraitAttribute($value) {
        if (empty($value))
            $this->attributes['extrait'] = Str::limit(strip_tags($this->contenu), config('application.extrait'));
        else
            $this->attributes['extrait'] = $value;
    }

    public function getEtatAttribute($value) {
        if ($this->publie == 1)
            return "Publié";
        else
            return "En attente";
    }

    public function categorie() {
        return $this->belongsTo('App\Http\Models\Categorie');
    }

    public function scopeOnline($query, $publie=1) {
        return $query->where('publie', $publie);
    }

}
