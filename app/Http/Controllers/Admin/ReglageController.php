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
use App\Http\Models\Matiere;
use App\Http\Models\Niveau;


use Illuminate\Http\Request;

class ReglageController extends Controller {

    public function __construct() {
        $this->middleware('edit');
    }

    //NIVEAUS
    public function getNiveaux($id = 0) {
        $niveau = new Niveau();
        if ($id != 0)
            $niveau = Niveau::find($id);
        $niveaus = Niveau::select('id', 'niveau','slug')->orderBy('niveau')->get();
        return view('admin.reglage.niveau')->with('niveau', $niveau)->with('niveaus', $niveaus);
    }

    public function getNiveau($id = 0) {
        $niveau = Niveau::find($id);
        $niveaus = Niveau::all();
        if ($niveau == null)
            $niveau = new Niveau();
        return view('admin.reglage.niveau')->with('niveau', $niveau)->with('niveaus', $niveaus);
    }

    public function postCreateNiveau(Request $request) {
        if ($request->isMethod('post')) {
            Niveau::create($request->all());
            return redirect('admin/reglages/niveaux');
        }
    }

    public function putUpdateNiveau(Request $request) {
        if ($request->isMethod('put')) {
            $id = $request->input('id');
            Niveau::find($id)->update($request->all());
            return redirect('admin/reglages/niveaux');
        }
    }

    public function getNiveauDelete($id, Request $request) {
        $ids = $request->input('delete_cats', $id);
        Niveau::destroy($ids);
        return redirect('admin/reglages/niveaux');
    }

    public function postNiveauAction(Request $request) {
        $action = $request->input('action');
        if ($action == 'delete')
            Niveau::destroy($request->input('id'));
        return redirect('admin/reglages/niveaux');
    }

    //CLASSES
    public function getClasses($id = 0) {
        $niveaux = Niveau::all();
        $classe = new Classe();
        if ($id != 0)
            $classe = Classe::find($id);
        $classes = Classe::with('niveau')->orderBy('classe')->get();
        return view('admin.reglage.classe')->with('classe', $classe)->with('classes', $classes)->with('niveaus',$niveaux);
    }

    public function getClasse($id = 0) {
        $niveaux = Niveau::all();
        $classe = Classe::find($id);
        $classes = Classe::all();
        if ($classe == null)
            $classe = new Classe();
        return view('admin.reglage.classe')->with('classe', $classe)->with('classes', $classes)->with('niveaus',$niveaux);
    }

    public function postCreateClasse(Request $request) {
        if ($request->isMethod('post')) {
            Classe::create($request->all());
            return redirect('admin/reglages/classes');
        }
    }

    public function putUpdateClasse(Request $request) {
        if ($request->isMethod('put')) {
            $id = $request->input('id');
            Classe::find($id)->update($request->all());
            return redirect('admin/reglages/classes');
        }
    }

    public function getClasseDelete($id, Request $request) {
        $ids = $request->input('delete_cats', $id);
        Classe::destroy($ids);
        return redirect('admin/reglages/classes');
    }

    public function postClasseAction(Request $request) {
        $action = $request->input('action');
        if ($action == 'delete')
            Classe::destroy($request->input('id'));
        return redirect('admin/reglages/classes');
    }


}