<?php namespace App\Http\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class Classe extends Model {

    public $fillable = ['classe', 'slug', 'niveau_id'];

    public function setSlugAttribute($value) {
        $this->attributes['slug'] = Str::slug($this->classe);
    }

    public function niveau() {
        return $this->belongsTo('App\Http\Models\Niveau');
    }

    public function cours() {
        return $this->belongsToMany('App\Http\Models\Cours', 'classe_cour', 'classe_id', 'cour_id');
    }

    /* public static function classeHtmlOptions($niveau) {

         $classes = self::with(['niveau' => function ($query) use ($niveau) {
              $query->where('slug', $niveau);
         }])->orderBy('classe')->get();

         $html = '<option value="">Classe</option>';

         foreach ($classes as $classe) {
             $html .= '<option value="' . $classe->slug . '">' . $classe->classe . '</option>';
         }
         return $classes;
         return $html;
     }*/
    public static function coursByMatiere($matiere) {
        $classes = self::with(['cours' => function ($query) use ($matiere) {
            $query->where('matiere_id', $matiere);
            $query->where('publie',1);
        }])->orderBy('classe','desc')->get();
        return $classes;
    }

}
