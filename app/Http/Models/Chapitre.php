<?php namespace App\Http\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class Chapitre extends Model {

    public $fillable = ['titre', 'contenu', 'vue', 'cour_id', 'image', 'publie','slug','etat','extrait'];

    public static $rules = ['titre' => 'required', 'contenu' => 'required'];

    public function cours() {
        return $this->belongsTo('App\Http\Models\Cours');
    }

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

    public function scopeOnline($query, $publie=1) {
        return $query->where('publie', $publie);
    }
}
