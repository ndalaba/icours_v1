<?php
/**
 * Created by PhpStorm.
 * User: ndalaba
 * Date: 26/05/15
 * Time: 10:17
 */

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Models\Corrige;
use App\Http\Models\Examen;
use App\Http\Models\Matiere;
use App\Http\Models\Session;

use App\Http\Models\Help;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class CorrigeController extends Controller {

    public function __construct() {
        $this->middleware('edit');
    }


    public function getIndex($matiere = 0) {

        if ($matiere != 0) {
            $corriges = Corrige::with('matiere', 'examens', 'sessions')->where('matiere_id', $matiere)->orderBy('id', 'desc')->get();

        } else
            $corriges = Corrige::with('matiere', 'examens', 'sessions')->orderBy('id', 'desc')->get();

        $matiere = Matiere::find($matiere);
        if ($matiere == null)
            $matiere = new Matiere();
        $data = [
            'corriges' => $corriges,
            'matieres' => Matiere::all(),
            'examens' => Examen::all(),
            'all' => Corrige::select('id')->count('id'),
            'online' => Corrige::select('id')->online(true)->count('id'),
            'filtre' => $matiere->matiere
        ];
        return view('admin.corrige.index', $data);
    }

    public function getIndexExamen($examen = 0) {
        if ($examen != 0) {
            $examen = Examen::find($examen);
            $corriges = $examen->corriges;
        } else
            $corriges = Corrige::with('matiere', 'examens', 'sessions')->orderBy('id', 'desc')->get();
        $data = [
            'corriges' => $corriges,
            'matieres' => Matiere::all(),
            'examens' => Examen::all(),
            'all' => Corrige::select('id')->count('id'),
            'online' => Corrige::select('id')->online(true)->count('id'),
            'filtre' => $examen->examen
        ];
        return view('admin.corrige.index', $data);
    }

    public function getIndexSession($session = 0) {
        if ($session != 0) {
            $session = Session::find($session);
            $corriges = $session->corriges;
        } else
            $corriges = Corrige::with('matiere', 'examens', 'sessions')->orderBy('id', 'desc')->get();
        $data = [
            'corriges' => $corriges,
            'matieres' => Matiere::all(),
            'examens' => Examen::all(),
            'all' => Corrige::select('id')->count('id'),
            'online' => Corrige::select('id')->online(true)->count('id'),
            'filtre' => $session->session
        ];
        return view('admin.corrige.index', $data);
    }


    public function getEtat($publie = 0) {
        $corriges = Corrige::with('matiere', 'sessions', 'examens')->online($publie)->orderBy('id', 'desc')->get();
        $data = [
            'corriges' => $corriges,
            'matieres' => Matiere::all(),
            'examens' => Examen::all(),
            'all' => Corrige::select('id')->count('id'),
            'online' => Corrige::select('id')->online(true)->count('id')
        ];
        return view('admin.corrige.index', $data);
    }

    public function postCorrigeAction(Request $request) {
        if ($request->input('doaction') == 'Appliquer') {
            $action = $request->input('action');
            if ($action == 'trash')
                Corrige::destroy($request->input('post'));

            return redirect('admin/corrige/index');
        } elseif ($request->input('doaction') == 'Filtrer') {
            $matiere = $request->input('mat');
            $exam = $request->input('exa');

            $query = Corrige::with('matiere', 'examens', 'sessions');
            if (!empty($matiere))
                $query->byMatiere($matiere);
            if (!empty($exam))
                $query->byExamen($exam);

            $corriges = $query->orderBy('id', 'desc')->get();

            $data = [
                'corriges' => $corriges,
                'matieres' => Matiere::all(),
                'examens' => Examen::all(),
                'all' => Corrige::select('id')->count('id'),
                'online' => Corrige::select('id')->online(true)->count('id'),
                'filtre' => $matiere . ' ' . $exam
            ];

            return view('admin.corrige.index', $data);
        }

    }

    public function getEdit(Request $request, $id = 0) {
        $corrige = new Corrige();
        $matieres = Matiere::all();
        $sessions = Session::orderBy('session', 'desc')->get();
        $examens = Examen::all();

        if (count($request->old()) && $id == 0) { // redirection après validation incorrect
            $corrige = $corrige->fill($request->old());

        } else {
            $corrige = Corrige::find($id);
            if ($corrige == null)
                $corrige = new Corrige();

            $corrigeexamens = $corrige->examens()->lists('examen', 'id');
            $corrigesessions = $corrige->sessions()->lists('session', 'id');
        }
        $data = ['corrige' => $corrige, 'matieres' => $matieres, 'sessions' => $sessions, 'examens' => $examens, 'corrigeexamens' => $corrigeexamens, 'corrigesessions' => $corrigesessions];
        return view('admin.corrige.formulaire', $data);
    }


    public function postStore(Request $request) {
        if ($request->isMethod('post')) {

            $corrige = new Corrige;

            $validator = \Validator::make($request->all(), Corrige::$rules);
            if ($validator->fails()) {
                return redirect('admin/corrige/edit/' . $request->input('id'))->withInput()->withErrors($validator->messages());
            }

            $request = Help::publie($request);
            //$request= Help::upload($request,'file','images/');

            if ($request->has('id')) {
                $corrige = Corrige::find($request->input('id'));
                $corrige->update($request->all());
            } else {
                if (Help::checkObject(new Corrige(), 'slug', Str::slug($request->get('titre')), $request->input('id', 0)))
                    return redirect('admin/corrige/edit/' . $request->input('id'))->withInput()->withErrors("Une épreuve ayant le même titre existe");
                $corrige = Corrige::create($request->all());
            }


            $corrige->examens()->sync($request->get('examens'));
            $corrige->sessions()->sync($request->get('sessions'));
            return redirect('admin/corriges/edit/' . $corrige->id)->with('success', 1);
        }
    }

    public function getDestroy($id) {
        Corrige::destroy($id);
        return redirect('admin/corriges/index/');
    }


    //EXAMENS
    public function getExamens($id = 0) {
        $examen = new Examen();
        if ($id != 0)
            $examen = Examen::find($id);
        $examens = Examen::select('id', 'examen', 'description')->orderBy('examen')->get();
        return view('admin.corrige.examen')->with('examen', $examen)->with('examens', $examens);
    }

    public function getExamen($id = 0) {
        $examen = Examen::find($id);
        $examens = Examen::all();
        if ($examen == null)
            $examen = new Examen();
        return view('admin.corrige.examen')->with('examen', $examen)->with('examens', $examens);
    }

    public function postCreateExamen(Request $request) {
        if ($request->isMethod('post')) {
            Examen::create($request->all());
            return redirect('admin/corriges/examens');
        }
    }

    public function putUpdateExamen(Request $request) {
        if ($request->isMethod('put')) {
            $id = $request->input('id');
            Examen::find($id)->update($request->all());
            return redirect('admin/corriges/examens');
        }
    }

    public function getExamenDelete($id, Request $request) {
        $ids = $request->input('delete_cats', $id);
        Examen::destroy($ids);
        return redirect('admin/corriges/examens');
    }

    public function postExamenAction(Request $request) {
        $action = $request->input('action');
        if ($action == 'delete')
            Examen::destroy($request->input('id'));
        return redirect('admin/corriges/examens');
    }

    //SESSION
    public function getSessions($id = 0) {
        $session = new Session();
        if ($id != 0)
            $session = Session::find($id);
        $sessions = Session::select('id', 'session')->orderBy('session', 'desc')->get();
        return view('admin.corrige.session')->with('session', $session)->with('sessions', $sessions);
    }

    public function getSession($id = 0) {
        $session = Session::find($id);
        $sessions = Session::all();
        if ($session == null)
            $session = new Session();
        return view('admin.corrige.session')->with('session', $session)->with('sessions', $sessions);
    }

    public function postCreateSession(Request $request) {
        if ($request->isMethod('post')) {
            Session::create($request->all());
            return redirect('admin/corriges/sessions');
        }
    }

    public function putUpdateSession(Request $request) {
        if ($request->isMethod('put')) {
            $id = $request->input('id');
            Session::find($id)->update($request->all());
            return redirect('admin/corriges/sessions');
        }
    }

    public function getSessionDelete($id, Request $request) {
        $ids = $request->input('delete_cats', $id);
        Session::destroy($ids);
        return redirect('admin/corriges/sessions');
    }

    public function postSessionAction(Request $request) {
        $action = $request->input('action');
        if ($action == 'delete')
            Session::destroy($request->input('id'));
        return redirect('admin/corriges/sessions');
    }
}