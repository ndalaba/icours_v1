<?php
namespace App\Http\Controllers\Front;

use App\Http\Controllers\Controller;
use App\Http\Models\Article;
use App\Http\Models\Categorie;


class ActualiteController extends Controller {
    var $paginate;

    public function  __construct() {
        $this->paginate = config('application.paginate');
    }

    public function actualites($cat = null) {
        $categories = Categorie::all();
        $categorie = Categorie::where('categorie', $cat)->first();

        if ($categorie != null)
            $articles = Article::with('categorie')->where('categorie_id', $categorie->id)->online()->simplePaginate($this->paginate);
        else
            $articles = Article::with('categorie')->online()->simplePaginate($this->paginate);

        $articles->setPath('actualites');

        return view('front.articles.articles')->with('categories', $categories)->with('articles', $articles)->with('categorie', $categorie);
    }

    public function actualite($categorie, $slug) {
        $categorie = Categorie::where('slug', $categorie)->first();
        $article = Article::where('slug', $slug)->first();
        if (is_null($categorie) || is_null($article))
            abort(404);
        $articles = Article::with('categorie')->where('categorie_id', $categorie->id)->online()->where('slug', '!=', $slug)->get();

        return view('front.articles.article')->with('articles', $articles)->with('article', $article)->with('categorie', $categorie);
    }
}
