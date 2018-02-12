<?php
/**
 * Created by PhpStorm.
 * User: N'Dalaba
 * Date: 04/08/2015
 * Time: 11:13
 */

namespace App\Http\Models;


use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\Request;

class Help {

    /**
     * Verifier l'existance d'un objet sur un champ
     * @param string $field
     * @param string $val
     * @param int $id
     * @return bool
     */
    public static function checkObject(Model $model, $field = '', $val = "", $id = 0) {
        $objet = $model::where($field, $val)->first();
        if ($objet != null) {
            // S'il s'agit du mm objet alors un autre objet n'existe pas
            if ($objet->id == $id)
                return false;
            else
                return true;
        } else
            return false;
    }

    /**On insiste pour attribuer une valeur au champ publie et revoie la requete
     * @param Request $request
     * @return Request
     */
    public static function publie(Request $request) {
        if (!$request->has('publie'))
            $request->merge(array('publie' => 0));
        else
            $request->merge(array('publie' => 1));
        return $request;
    }

    public static function uploadImage(Request $request, $file, $destination) {
        if ($request->hasFile($file)) {
            if ($request->file($file)->isValid()) {
                // $extension = $request->file($file)->getClientOriginalExtension(); // getting image extension
                $fileName = $request->file($file)->getClientOriginalName(); // renaming image
                $request->file($file)->move('uploads/' . $destination, $fileName); // uploading file to given path
                $request->merge(array('image' => $fileName));
            }
        }
        return $request;
    }

    public static function uploadFichier(Request $request, $file, $destination) {
        if ($request->hasFile($file)) {
            if ($request->file($file)->isValid()) {
                // $extension = $request->file($file)->getClientOriginalExtension(); // getting image extension
                $fileName = $request->file($file)->getClientOriginalName(); // renaming image
                $request->file($file)->move('uploads/' . $destination, $fileName); // uploading file to given path
            }
        }
        return $request;
    }

    public static function basename($file) {
        $pathinfo = pathinfo($file);
        return $pathinfo['basename'];
    }
    public static function timestampToDate($value) {
        return date('d/m/Y H:i', strtotime($value));
    }

}