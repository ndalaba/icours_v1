<?php namespace App\Http\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class Cours extends Model {

    public $fillable = ['titre', 'contenu', 'vue', 'matiere_id', 'image', 'publie', 'etat', 'slug', 'extrait','tag'];

    public static $rules = ['titre' => 'required', 'contenu' => 'required'];

    public function matiere() {
        return $this->belongsTo('App\Http\Models\Matiere');
    }

    public function  classes() {
        return $this->belongsToMany('App\Http\Models\Classe', 'classe_cour', 'cour_id', 'classe_id');
    }

    public function chapitres() {
        return $this->hasMany('App\Http\Models\Chapitre','cour_id','id');
    }

    public function niveau(){
        return $this->hasManyThrough('App\Http\Models\Niveau','App\Http\Models\Classe');
    }


    public function setSlugAttribute($value) {
        /*if (empty($value))
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

    public function scopeOnline($query, $publie = 1) {
        return $query->where('publie', $publie);
    }

    public function scopeByMatiere($query, $matiere) {
        return $query->whereHas('matiere', function ($q) use ($matiere) {
            return $q->where('slug', $matiere);
        });
    }

    public function scopeByClasse($query, $classe) {
        return $query->whereHas('classes', function ($q) use ($classe) {
            return $q->where('slug', $classe);
        });
    }

    public function scopeByNiveau($query, $niveau) {
        return $query->whereHas('niveau', function ($q) use ($niveau) {
            return $q->where('slug', $niveau);
        });
    }

    public function scopeByContent($query, $titre) {
        $s = explode(" ", $titre);
        $q = false; // verifier si aumoins une valeur de recherche est valide
        $query->where(function ($query) use ($s, &$q) {
            foreach ($s as $value) {
                if (strlen($value) >= 4) {
                    $query->where('contenu', 'like', '%' . $value . '%');
                    $query->orWhere('titre', 'like', '%' . $value . '%');
                    $query->orWhere('tag', 'like', '%' . $value . '%');
                    $q = true;
                }
            }
        });
        if ($q)
            return $query;
        else
            return $query->where('titre', '1');
    }

    public static function countCours() {
        $sql = 'SELECT matieres.id, matieres.matiere, matieres.slug, count(*) as nbre from cours INNER JOIN matieres on matiere_id=matieres.id where cours.publie=1 group by matiere_id';
        return \DB::select(\DB::raw($sql));
    }

    public static function derniers($n = 4) {
        return self::with('matiere')->online()->orderBy('id', 'desc')->take($n)->get();
    }

}
