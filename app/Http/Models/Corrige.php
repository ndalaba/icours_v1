<?php namespace App\Http\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class Corrige extends Model {

    public $fillable = ['titre', 'sujet', 'slug', 'extrait', 'corrige', 'matiere_id', 'session', 'publie', 'etat'];

    public static $rules = ['sujet' => 'required', 'matiere_id' => 'required'];

    public function setSlugAttribute($value) {
       /*ù if (empty($value))
            $this->attributes['slug'] = Str::slug($this->titre);
        else
            $this->attributes['slug'] = Str::slug($value);*/
        $this->attributes['slug'] = Str::slug($this->titre);
    }

    public function setExtraitAttribute($value) {
        if (empty($value))
            $this->attributes['extrait'] = Str::limit(strip_tags($this->sujet), config('application.extrait'));
        else
            $this->attributes['extrait'] = $value;

    }

    public function matiere() {
        return $this->belongsTo('App\Http\Models\Matiere');
    }

    public function examens() {
        return $this->belongsToMany('App\Http\Models\Examen');
    }

    public function sessions() {
        return $this->belongsToMany('App\Http\Models\Session');
    }


    public function scopeOnline($query, $publie = 1) {
        return $query->where('publie', $publie);
    }

    public function scopeBySession($query, $session) {
        return $query->whereHas('sessions', function ($q) use ($session) {
            return $q->where('session', $session);
        });
    }

    public function scopeByMatiere($query, $matiere) {
        return $query->whereHas('matiere', function ($q) use ($matiere) {
            return $q->where('slug', $matiere);
        });
    }

    public function scopeByExamen($query, $examen) {

        return $query->whereHas('examens', function ($q) use ($examen) {
            return $q->where('examen', $examen);
        });
    }


}
