<?php namespace App\Http\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class Etude extends Model {

    public $fillable = ['nom', 'contenu', 'adresse', 'type_id', 'publie', 'extrait', 'image', 'slug', 'etat'];

    public static $rules = ['nom' => 'required', 'contenu' => 'required'];

    public function type() {
        return $this->belongsTo('App\Http\Models\Type');
    }

    public function setSlugAttribute($value) {
       /* if (empty($value))
            $this->attributes['slug'] = Str::slug($this->nom);
        else
            $this->attributes['slug'] = Str::slug($value);*/
        $this->attributes['slug'] = Str::slug($this->nom);
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

    public function scopeOnline($query, $publie = 1) {
        return $query->where('publie', $publie);
    }

    public static function derniers($n = 3) {
        return self::with('type')->online()->orderBy('id', 'desc')->take($n)->get();
    }

}
