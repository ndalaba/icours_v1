<?php
namespace App\Http\Controllers\Front;

use App\Http\Controllers\Controller;
use App\Http\Models\Corrige;
use App\Http\Models\Examen;
use App\Http\Models\Matiere;
use App\Http\Models\Session;
use Illuminate\Http\Request;


class CorrigeController extends Controller {
    var $paginate;

    public function  __construct() {
        $this->paginate = config('application.paginate');
    }

    public function corriges() {
        $matieres = Matiere::all();
        $sessions = Session::orderBy('session','desc')->get();
        $examens = Examen::all();

        $corriges = Corrige::with(['matiere', 'examens', 'sessions' => function ($query) {
            return $query->orderBy('session', 'desc');
        }])->online()->simplePaginate($this->paginate);

        $corriges->setPath('corriges');

        $data = ['corriges' => $corriges, 'matieres' => $matieres, 'examens' => $examens, 'sessions' => $sessions];

        return view('front.corriges.corriges', $data);
    }

    public function corrige($matiere, $slug) {

        $matieres = Matiere::all();
        $sessions = Session::orderBy('session','desc')->get();
        $examens = Examen::all();

        $corrige = Corrige::where('slug', $slug)->first();
        if (is_null($corrige))
            abort(404);

        $corriges = Corrige::with('session', 'examen', 'matiere')->where('matiere_id', $corrige->matiere_id)->online()->where('slug', '!=', $slug)->get();

        $data = ['corriges' => $corriges, 'corrige' => $corrige, 'matieres' => $matieres, 'examens' => $examens, 'sessions' => $sessions];
        return view('front.corriges.corrige', $data);
    }

    public function recherche(Request $request) {
        $matieres = Matiere::all();
        $sessions = Session::orderBy('session','desc')->get();
        $examens = Examen::all();

        $matiere = $request->get('matiere');
        $session = $request->get('session');
        $examen = $request->get('examen');

        $query = Corrige::with('matiere', 'examens', 'sessions')->online();
        if (!empty($matiere))
            $query->byMatiere($matiere);
        if (!empty($session))
            $query->bySession($session);
        if (!empty($examen))
            $query->byExamen($examen);

        $corriges = $query->simplePaginate($this->paginate);
        $corriges->setPath('recherche');

        $data = ['corriges' => $corriges, 'matieres' => $matieres, 'examens' => $examens, 'sessions' => $sessions, 'recherche' => $matiere . ' ' . $examen . ' ' . $session];
        return view('front.corriges.resultats', $data);
    }
}
