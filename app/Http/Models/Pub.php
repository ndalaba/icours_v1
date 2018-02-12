<?php namespace App\Http\Models;

use Illuminate\Database\Eloquent\Model;

class Pub extends Model {

    public $fillable = ['titre', 'contenu', 'image', 'lien', 'niveau', 'debut', 'fin', 'entreprise'];


}
