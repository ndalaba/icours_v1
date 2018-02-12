<?php

/**
 * Description of User Controller
 *
 * @author NDalaba
 */

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;


use App\Http\Models\User;
use App\Http\Models\Help;
use Illuminate\Http\Request;

class UserController extends Controller {

    public function __construct() {
        $this->middleware('admin');
    }

    public function getIndex() {
        return view('admin.user.index')->with('users', User::all());
    }

    public function getEdit(Request $request,$id = 0) {
        $user = new User();
        if (count($request->old())) { // redirection après validation incorrect
            $user = $user->fill($request->old());
        } else {
            $user = User::find($id);
            if ($user == null)
                $user = new User();
        }
        return view('admin.user.formulaire')->with('user', $user);
    }

    public function postStore(Request $request) {
        if ($request->isMethod('post')) {

            $user = new User;
            $validator = \Validator::make($request->all(), User::$rules);
            if ($validator->fails()) {
                return redirect('admin/users/edit/' . $request->input('id'))->withInput()->withErrors($validator->messages());
            }

            //$request= Help::upload($request,'file','images/');
            $error = "";
            if (Help::checkObject(new User(), 'email', $request->input('email'),$request->input('id',0)))
                $error = "Adresse email déja enregistrée";

            if (Help::checkObject(new User(), 'email', $request->input('phone'),$request->input('id',0)))
                $error = "Numéro de téléphone déja enregistré";

            if (strlen(trim($error)) > 0)
                return view('admin.user.formulaire', ['user' => $user, 'error' => $error]);

            $pass = ($request->input('password') == "") ? $request->input('lastpass') : \Hash::make($request->input('password'));
            $request->merge(['password' => $pass]);

            if ($request->has('id')) {
                $user = User::find($request->input('id'));
                $user->update($request->all());
            } else
                $user = User::create($request->all());

            return view('admin.user.formulaire', ['user' => $user, 'success' => 1]);
        }
    }

    public function getDestroy($id) {
        $user = User::find($id);
        if ($user->droit < 10)
            User::destroy($id);
        return redirect('admin/users/index');
    }
}
