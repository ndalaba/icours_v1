<?php
/**
 * Created by PhpStorm.
 * User: ndalaba
 * Date: 26/05/15
 * Time: 10:17
 */

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Models\Etude;
use App\Http\Models\Type;

use App\Http\Models\Help;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class EtudeController extends Controller {

    public function __construct() {
        $this->middleware('edit');
    }

    public function getIndex($type = 0) {
        if ($type != 0)
            $etudes = Etude::with('type')->where('type_id', $type)->orderBy('id', 'desc')->get();
        else
            $etudes = Etude::with('type')->orderBy('id', 'desc')->get();

        $data = [
            'etudes' => $etudes,
            'types' => Type::select('id', 'type')->get(),
            'all' => Etude::select('id')->count('id'),
            'online' => Etude::select('id')->online(true)->count('id')
        ];
        return view('admin.etude.index', $data);
    }

    public function getEtat($publie = 0) {
        $etudes = Etude::with('type')->online($publie)->orderBy('id', 'desc')->get();
        $data = [
            'etudes' => $etudes,
            'types' => Type::select('id', 'type')->get(),
            'all' => Etude::select('id')->count('id'),
            'online' => Etude::select('id')->online(true)->count('id')
        ];
        return view('admin.etude.index', $data);
    }

    public function postEtudeAction(Request $request) {
        if ($request->input('doaction') == 'Appliquer') {
            $action = $request->input('action');
            if ($action == 'trash')
                Etude::destroy($request->input('post'));

            return redirect('admin/etudes/index');
        } elseif ($request->input('doaction') == 'Filtrer') {
            return redirect('admin/etudes/index/' . $request->input('cat'));
        }

    }

    public function getEdit(Request $request, $id = 0) {
        $etude = new Etude();
        $types = Type::all();
        if (count($request->old()) && $id == 0) { // redirection après validation incorrect
            $etude = $etude->fill($request->old());
        } else {
            $etude = Etude::find($id);
            if ($etude == null)
                $etude = new Etude();
        }
        return view('admin.etude.formulaire')->with('etude', $etude)->with('types', $types);
    }


    public function postStore(Request $request) {
        if ($request->isMethod('post')) {

            $types = Type::select('id', 'type')->get();
            $etude = new Etude;

            $validator = \Validator::make($request->all(), Etude::$rules);
            if ($validator->fails()) {
                return redirect('admin/etudes/edit/' . $request->input('id'))->withInput()->withErrors($validator->messages());
            }

            $request = Help::publie($request);
            //$request= Help::upload($request,'file','images/');

            if ($request->has('id')) {
                $etude = Etude::find($request->input('id'));
                $etude->update($request->all());
            } else {
                if (Help::checkObject(new Etude(), 'slug', Str::slug($request->get('nom')), $request->input('id', 0)))
                    return redirect('admin/etudes/edit/' . $request->input('id'))->withInput()->withErrors("Un établissement ayant le même nom existe!");
                $etude = Etude::create($request->all());
            }

            return redirect('admin/etudes/edit/' . $etude->id)->with('success', 1);
        }
    }


    public function getDestroy($id) {
        Etude::destroy($id);
        return redirect('admin/etudes/index/');
    }

    //TYPES
    public function getTypes($id = 0) {
        $type = new Type();
        if ($id != 0)
            $type = Type::find($id);
        $types = Type::all();
        return view('admin.etude.type')->with('type', $type)->with('types', $types);
    }

    public function getType($id = 0) {
        $type = Type::find($id);
        $types = Type::all();
        if ($type == null)
            $type = new Type();
        return view('admin.etude.type')->with('type', $type)->with('types', $types);
    }

    public function postCreateType(Request $request) {
        if ($request->isMethod('post')) {
            Type::create($request->all());
            return redirect('admin/etudes/types');
        }
    }

    public function putUpdateType(Request $request) {
        if ($request->isMethod('put')) {
            $id = $request->input('id');
            Type::find($id)->update($request->all());
            return redirect('admin/etudes/types');
        }
    }

    public function getTypeDelete($id, Request $request) {
        $ids = $request->input('delete_cats', $id);
        Type::destroy($ids);
        return redirect('admin/etudes/types');
    }

    public function postTypeAction(Request $request) {
        $action = $request->input('action');
        if ($action == 'delete')
            Type::destroy($request->input('id'));
        return redirect('admin/etudes/types');
    }
}