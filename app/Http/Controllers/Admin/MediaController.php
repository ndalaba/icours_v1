<?php
/**
 * Created by PhpStorm.
 * User: N'Dalaba
 * Date: 06/08/2015
 * Time: 10:35
 */

namespace App\Http\Controllers\Admin;


use App\Http\Controllers\Controller;
use App\Http\Models\Help;
use Illuminate\Http\Request;

class MediaController extends Controller {

    public function postDeleteImage(Request $request,$media = null) {
        $media = $request->input('image');
        if (\File::exists('uploads/images/' . $media))
            \File::delete('uploads/images/' . $media);
    }

    public function postDeleteFichier(Request $request,$media = null) {
        $media = $request->input('image');
        if (\File::exists('uploads/fichiers/' . $media))
            \File::delete('uploads/fichiers/' . $media);
    }
    public function postUploadImage(Request $request){
        Help::uploadImage($request,'upl','images');
        echo '{"status":"success"}';
    }
    public function postUploadFichier(Request $request){
        Help::uploadFichier($request,'upl','fichiers');
        echo '{"status":"success"}';
    }
    public function getImages(){
        $images= \File::files('uploads/images');
         return view('admin.inc.imagelist')->with('images',$images);
    }
}