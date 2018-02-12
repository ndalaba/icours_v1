<?php
namespace App\Http\Controllers\Front;

use App\Http\Controllers\Controller;
use App\Http\Models\Cours;
use App\Http\Models\Etude;
use App\Http\Models\Matiere;
use Illuminate\Http\Request;

class HomeController extends Controller {


    public function index() {
         $data=array(
            'courscount'=>\App\Http\Models\Cours::select('id')->count('id'),
            'chapitres'=>\App\Http\Models\Chapitre::select('id')->count('id'),
            'classes'=>\App\Http\Models\Classe::select('id')->count('id'),
            'matieres'=>\App\Http\Models\Matiere::select('id')->count('id'),
            'etudescount'=>\App\Http\Models\Etude::select('id')->count('id'),
            'statistiques' => Cours::countCours(),
            'cours' => Cours::derniers(),
            'etudes' => Etude::derniers(),
            
        );        
        return view('front.home', $data);
    }

    public function contact(Request $request) {
        if ($request->isMethod('post')) {
            $validator = \Validator::make($request->all(), ['email' => 'required|email', 'nom' => 'required', 'question' => 'required', 'details' => 'required']);
            if ($validator->fails()) {
                return redirect('nous-contacter')->withInput()->withErrors($validator->messages());
            }

            \Mail::send('emails.contact', ['request' => $request->all()], function ($message) use ($request) {
                $message->from($request->input('email'), $request->input('nom'));
                $message->to('contact@icours.com', config('application.name'))->subject($request->input('question'));
            });
            return view('front.contact')->with('success', 'Message envoyé');
        } else
            return view('front.contact');
    }

    public function about() {
        return view('front.about');
    }

    public function fonctionnement() {
        return view('front.fonctionnement');
    }
    public function conseils() {
        $matieres = Matiere::orderBy('matiere')->get();
        return view('front.conseil')->with('matieres',$matieres);
    }

}
