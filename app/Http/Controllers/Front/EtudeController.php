<?php
namespace App\Http\Controllers\Front;

use App\Http\Controllers\Controller;
use App\Http\Models\Etude;
use App\Http\Models\Type;
use Illuminate\Http\Request;


class EtudeController extends Controller {
    var $paginate;

    public function  __construct() {
        $this->paginate = config('application.paginate');
    }
    public function recherche(Request $request) {
        $q = $request->get('q');
        $types = Type::all();
        $etudes= Etude::with('type')->where('nom', 'like', '%' . $q . '%')->get();

        return view('front.etudes.resultats')->with('types', $types)->with('etudes', $etudes)->with('recherche',$q);
    }

    public function etudes($typ = null) {
        $types = Type::all();
        $type = Type::where('slug', $typ)->first();

        if ($type != null)
            $etudes = Etude::with('type')->where('type_id', $type->id)->online()->simplePaginate($this->paginate);
        else
            $etudes = Etude::with('type')->online()->simplePaginate($this->paginate);

        $etudes->setPath('etudes');

        return view('front.etudes.etudes')->with('types', $types)->with('etudes', $etudes)->with('type', $type);
    }

    public function etude($type, $slug) {
        $type = Type::where('slug', $type)->first();
        $etude = Etude::where('slug', $slug)->first();
        if (is_null($type) || is_null($etude))
            abort(404);
        $etudes = Etude::with('type')->where('type_id', $type->id)->online()->where('slug', '!=', $slug)->get();

        return view('front.etudes.etude')->with('etudes', $etudes)->with('etude', $etude)->with('type', $type);
    }
}
