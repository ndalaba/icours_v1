<?php
namespace App\Http\Controllers\Front;

use App\Http\Controllers\Controller;
use App\Http\Models\Chapitre;
use App\Http\Models\Classe;
use App\Http\Models\Cours;
use App\Http\Models\Matiere;
use App\Http\Models\Niveau;
use Illuminate\Http\Request;

class CoursController extends Controller {

    var $paginate;

    public function  __construct() {
        $this->paginate = config('application.paginate');
    }

    public function cours($mat = null) {
        $matieres = Matiere::has('cours')->orderBy('matiere')->get();
        $classes = Classe::has('cours')->orderBy('classe')->get();

        $matiere = Matiere::where('slug', $mat)->first();

        if ($matiere != null) {
            $class= Classe::coursByMatiere($matiere->id);
            return view('front.cours.coursmatiere')->with('matieres', $matieres)->with('class', $class)->with('matiere', $matiere)->with('classes', $classes);
        } else {
            $cours = Cours::with('matiere','chapitres')->orderBy('id','desc')->online()->simplePaginate($this->paginate);
            $cours->setPath('cours');
            return view('front.cours.cours')->with('matieres', $matieres)->with('cours', $cours)->with('matiere', $matiere)->with('classes', $classes);
        }

    }

    public function cour($matiere, $slug) {
        $matieres = Matiere::has('cours')->orderBy('matiere')->get();
        $classes = Classe::has('cours')->orderBy('classe')->get();

        $matiere = Matiere::where('slug', $matiere)->first();
        $cour = Cours::where('slug', $slug)->first();
        if (is_null($cour) || is_null($matiere))
            abort(404);
        $chapitres = Chapitre::where('cour_id', $cour->id)->online()->get();

        return view('front.cours.cour')->with('cour', $cour)->with('matiere', $matiere)->with('chapitres', $chapitres)->with('matieres', $matieres)->with('classes', $classes);
    }

    public function chapitre($mat, $cours, $slug) {
        $matieres = Matiere::has('cours')->orderBy('matiere')->get();
        $classes = Classe::has('cours')->orderBy('classe')->get();

        $matiere = Matiere::where('slug', $mat)->first();
        $cours = Cours::select('id', 'slug', 'titre', 'extrait')->where('slug', $cours)->first();
        $chapitre = Chapitre::where('slug', $slug)->first();
        if (is_null($cours) || is_null($matiere) || is_null($chapitre))
            abort(404);
        $chapitres = Chapitre::where('cour_id', $cours->id)->online()->get();
        $suivant = Chapitre::select('id', 'slug', 'titre')->where('cour_id', $cours->id)->where('id', '>', $chapitre->id)->online()->first();
        $precedent = Chapitre::select('id', 'slug', 'titre')->where('cour_id', $cours->id)->where('id', '<', $chapitre->id)->online()->orderBy('id', 'desc')->first();
        return view('front.cours.chapitre')->with('chapitre', $chapitre)->with('cours', $cours)->with('chapitres', $chapitres)->with('matiere', $matiere)->with('suivant', $suivant)->with('precedent', $precedent)->with('matieres', $matieres)->with('classes', $classes);
    }

    public function recherche(Request $request) {
        $classes = Classe::has('cours')->orderBy('classe')->get();
        $matieres = Matiere::has('cours')->orderBy('matiere')->get();

        $cour = $request->input('q');
        $cours = Cours::with('matiere')->online()->byContent($cour)->get();//->simplePaginate($this->paginate);
        //$cours->setPath('cours?q='.$cour);
        return view('front.cours.resultats')->with('matieres', $matieres)->with('cours', $cours)->with('matiere', null)->with('recherche', $cour)->with('matieres', $matieres)->with('classes', $classes);
    }

    public function recherchecours(Request $request) {
        $matieres = Matiere::has('cours')->orderBy('matiere')->get();
        $classes = Classe::has('cours')->orderBy('classe')->get();

        $q = $request->get('q');
        $niveau = $request->get('niveau');
        $matiere = $request->get('matiere');
        $classe = $request->get('classe');

        $mat = Matiere::where('slug', $matiere)->first();


        $query = Cours::with('matiere', 'classes')->online();
        if (!empty($q))
            $query->byContent($q);
        if (!empty($matiere))
            $query->byMatiere($matiere);
        /*if (!empty($niveau))
            $query->byNiveau($niveau);*/
        if (!empty($classe))
            $query->byClasse($classe);

        $cours = $query->simplePaginate($this->paginate);
        $cours->setPath('recherche-cours');

        $data = ['cours' => $cours, 'matieres' => $matieres, 'matiere' => $mat, 'classes' => $classes, 'recherche' => $q . ' ' . $matiere . ' ' . $classe . ' ' . $niveau];
        return view('front.cours.resultats', $data);
    }

}
