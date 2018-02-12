<?php
/**
 * Created by PhpStorm.
 * User: ndalaba
 * Date: 26/05/15
 * Time: 10:17
 */

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Models\Classe;
use App\Http\Models\Cours;
use App\Http\Models\Chapitre;
use App\Http\Models\Matiere;

use App\Http\Models\Help;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class CoursController extends Controller {

    public function __construct() {
        $this->middleware('edit');
    }

    public function getIndex($matiere = 0) {
        if ($matiere != 0)
            $cours = Cours::with('matiere')->where('matiere_id', $matiere)->orderBy('id', 'desc')->get();
        else
            $cours = Cours::with('matiere')->orderBy('id', 'desc')->get();
        $data = [
            'cours' => $cours,
            'matieres' => Matiere::select('id', 'matiere')->get(),
            'all' => Cours::select('id')->count('id'),
            'online' => Cours::select('id')->online(true)->count('id')
        ];
        return view('admin.cours.index', $data);
    }

    public function getEtat($publie = 0) {
        $cours = Cours::with('matiere')->online($publie)->orderBy('id', 'desc')->get();
        $data = [
            'cours' => $cours,
            'matieres' => Matiere::select('id', 'matiere')->get(),
            'all' => Cours::select('id')->count('id'),
            'online' => Cours::select('id')->online(true)->count('id')
        ];
        return view('admin.cours.index', $data);
    }

    public function postCoursAction(Request $request) {
        if ($request->input('doaction') == 'Appliquer') {
            $action = $request->input('action');
            if ($action == 'trash')
                Cours::destroy($request->input('post'));

            return redirect('admin/cours/index');
        } elseif ($request->input('doaction') == 'Filtrer') {
            return redirect('admin/cours/index/' . $request->input('mat'));
        }

    }

    public function getEdit(Request $request, $id = 0) {
        $cours = new Cours();
        $matieres = Matiere::all();
        if (count($request->old()) && $id == 0) { // redirection après validation incorrect
            $cours = $cours->fill($request->old());
        } else {
            $cours = Cours::find($id);
            if ($cours == null)
                $cours = new Cours();
        }
        $coursclasses = $cours->classes()->lists('classe', 'id', 'slug');
        $classes = Classe::orderBy('classe')->get();
        return view('admin.cours.formulaire')->with('cours', $cours)->with('matieres', $matieres)->with('coursclasses', $coursclasses)->with('classes', $classes);
    }


    public function postStore(Request $request) {
        if ($request->isMethod('post')) {

            /*$matieres = Matiere::select('id', 'matiere')->get();
            $cours = new Cours;*/

            $validator = \Validator::make($request->all(), Cours::$rules);
            if ($validator->fails()) {
                return redirect('admin/cours/edit/' . $request->input('id'))->withInput()->withErrors($validator->messages());
            }
            if (!count($request->get('classes'))) {
                return redirect('admin/cours/edit/' . $request->input('id'))->withInput()->withErrors("Aucune classe n'est séléctionnée");
            }

            $request = Help::publie($request);
            //$request= Help::upload($request,'file','images/');

            if ($request->has('id')) {
                $cours = Cours::find($request->input('id'));
                $cours->update($request->all());
            } else {
                if (Help::checkObject(new Cours(), 'slug', Str::slug($request->get('titre')), $request->input('id', 0)))
                    return redirect('admin/cours/edit/' . $request->input('id'))->withInput()->withErrors("Un cours ayant le même titre existe");
                $cours = Cours::create($request->all());
            }
            $cours->classes()->sync($request->get('classes'));
            return redirect('admin/cours/edit/' . $cours->id)->with('success', 1);
        }
    }

    public function getDestroy($id) {
        Cours::destroy($id);
        return redirect('admin/cours/index/');
    }


    //Chapitre

    public function getChapitres($cours = 0) {
        if ($cours != 0)
            $Chapitre = Chapitre::where('cour_id', $cours)->orderBy('id', 'desc')->get();
        else
            $Chapitre = Chapitre::orderBy('id', 'desc')->get();
        $cours = Cours::select('id', 'titre', 'matiere_id', 'slug')->where('id', $cours)->first();
        $data = [
            'chapitres' => $Chapitre,
            'cours' => $cours,
            'matiere' => Matiere::find($cours->matiere_id),
            'all' => count(Chapitre::select('id')->where('cour_id', $cours)->get()),
            'online' => count(Chapitre::select('id')->where('cour_id', $cours)->online(true)->get())
        ];
        return view('admin.cours.chapitres', $data);
    }

    public function getEtatChapitres($cours, $publie = 0) {
        $Chapitre = Chapitre::where('cour_id', $cours)->online($publie)->orderBy('id', 'desc')->get();
        $data = [
            'chapitres' => $Chapitre,
            'cours' => Cours::select('id', 'titre', 'slug')->where('id', $cours)->first(),
            'all' => count(Chapitre::select('id')->where('cour_id', $cours)->get()),
            'online' => count(Chapitre::select('id')->where('cour_id', $cours)->online(true)->get())
        ];
        return view('admin.cours.chapitres', $data);
    }

    public function postChapitreAction(Request $request) {
        if ($request->input('doaction') == 'Appliquer') {
            $action = $request->input('action');
            if ($action == 'trash')
                Chapitre::destroy($request->input('post'));

            return redirect('admin/cours/chapitres/' . $request->input('cours'));
        } elseif ($request->input('doaction') == 'Filtrer') {
            return redirect('admin/cours/chapitres/' . $request->input('cours'));
        }

    }

    public function getEditChapitre(Request $request, $cours, $id = 0) {
        $chapitre = new Chapitre();
        $cours = Cours::select('id', 'titre', 'matiere_id', 'slug')->where('id', $cours)->first();
        if (count($request->old()) && $id == 0) { // redirection après validation incorrect
            $chapitre = $chapitre->fill($request->old());
        } else {
            $chapitre = Chapitre::find($id);
            if ($chapitre == null)
                $chapitre = new Chapitre();
        }
        $matiere = Matiere::find($cours->matiere_id);
        return view('admin.cours.chapitre-formulaire')->with('chapitre', $chapitre)->with('cours', $cours)->with('matiere', $matiere);
    }


    public function postStoreChapitre(Request $request) {
        if ($request->isMethod('post')) {

            $cours = Cours::select('id', 'titre')->where('id', $request->input('cour_id'))->first();
            $Chapitre = new Chapitre;

            $validator = \Validator::make($request->all(), Chapitre::$rules);
            if ($validator->fails()) {
                return redirect('admin/cours/edit-chapitre/' . $request->input('cour_id'))->withInput()->withErrors($validator->messages());
            }

            $request = Help::publie($request);
            //$request= Help::upload($request,'file','images/');

            if ($request->has('id')) {
                $Chapitre = Chapitre::find($request->input('id'));
                $Chapitre->update($request->all());
            } else {
                if (Help::checkObject(new Chapitre(), 'slug', Str::slug($request->get('titre')), $request->input('id', 0)))
                    return redirect('admin/cours/edit-chapitre/' . $request->input('cour_id'))->withInput()->withErrors("Un chapitre ayant le même titre existe");
                $Chapitre = Chapitre::create($request->all());
            }

            return redirect('admin/cours/edit-chapitre/' . $Chapitre->cour_id . '/' . $Chapitre->id)->with('success', 1);

        }
    }

    public function getDestroyChapitre($cours, $id) {
        Chapitre::destroy($id);
        return redirect('admin/cours/chapitres/' . $cours);
    }

    //MATIERES
    public function getMatieres($id = 0) {
        $matiere = new Matiere();
        if ($id != 0)
            $matiere = Matiere::find($id);
        $matieres = Matiere::select('id', 'matiere', 'description')->orderBy('matiere')->get();
        return view('admin.cours.matiere')->with('matiere', $matiere)->with('matieres', $matieres);
    }

    public function getMatiere($id = 0) {
        $matiere = Matiere::find($id);
        $matieres = Matiere::all();
        if ($matiere == null)
            $matiere = new Matiere();
        return view('admin.cours.matiere')->with('matiere', $matiere)->with('matieres', $matieres);
    }

    public function postCreateMatiere(Request $request) {
        if ($request->isMethod('post')) {
            Matiere::create($request->all());
            return redirect('admin/cours/matieres');
        }
    }

    public function putUpdateMatiere(Request $request) {
        if ($request->isMethod('put')) {
            $id = $request->input('id');
            Matiere::find($id)->update($request->all());
            return redirect('admin/cours/matieres');
        }
    }

    public function getMatiereDelete($id, Request $request) {
        $ids = $request->input('delete_cats', $id);
        Matiere::destroy($ids);
        return redirect('admin/cours/matieres');
    }

    public function postMatiereAction(Request $request) {
        $action = $request->input('action');
        if ($action == 'delete')
            Matiere::destroy($request->input('id'));
        return redirect('admin/cours/matieres');
    }
}