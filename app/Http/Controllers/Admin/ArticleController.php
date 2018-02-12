<?php
/**
 * Created by PhpStorm.
 * User: ndalaba
 * Date: 26/05/15
 * Time: 10:17
 */

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Models\Article;
use App\Http\Models\Categorie;

use App\Http\Models\Help;
use Illuminate\Http\Request;

class ArticleController extends Controller {

    public function __construct() {
        $this->middleware('edit');
    }

    public function getIndex($categorie = 0) {
        if ($categorie != 0)
            $articles = Article::with('categorie')->where('categorie_id', $categorie)->orderBy('id', 'desc')->get();
        else
            $articles = Article::with('categorie')->orderBy('id', 'desc')->get();
        $data = [
            'articles' => $articles,
            'categories' => Categorie::select('id', 'categorie')->get(),
            'all' => Article::select('id')->count('id'),
            'online' => Article::select('id')->online(true)->count('id')
        ];
        return view('admin.article.index', $data);
    }

    public function getEtat($publie = 0) {
        $articles = Article::with('categorie')->online($publie)->orderBy('id', 'desc')->get();
        $data = [
            'articles' => $articles,
            'categories' => Categorie::select('id', 'categorie')->get(),
            'all' => Article::select('id')->count('id'),
            'online' => Article::select('id')->online(true)->count('id')
        ];
        return view('admin.article.index', $data);
    }

    public function postArticleAction(Request $request) {
        if ($request->input('doaction') == 'Appliquer') {
            $action = $request->input('action');
            if ($action == 'trash')
                Article::destroy($request->input('post'));

            return redirect('admin/articles/index');
        } elseif ($request->input('doaction') == 'Filtrer') {
            return redirect('admin/articles/index/' . $request->input('cat'));
        }

    }

    public function getEdit(Request $request, $id = 0) {
        $article = new Article();
        $categories = Categorie::all();
        if (count($request->old()) && $id == 0) { // redirection après validation incorrect
            $article = $article->fill($request->old());
        } else {
            $article = Article::find($id);
            if ($article == null)
                $article = new Article();
        }
        return view('admin.article.formulaire')->with('article', $article)->with('categories', $categories);
    }


    public function postStore(Request $request) {
        if ($request->isMethod('post')) {

            $categories = Categorie::select('id', 'categorie')->get();
            $article = new Article;

            $validator = \Validator::make($request->all(), Article::$rules);
            if ($validator->fails()) {
                return redirect('admin/articles/edit/' . $request->input('id'))->withInput()->withErrors($validator->messages());
            }

            $request = Help::publie($request);
            //$request= Help::upload($request,'file','images/');

            if ($request->has('id')) {
                $article = Article::find($request->input('id'));
                $article->update($request->all());
            } else {
                if (Help::checkObject(new Article(), 'slug', Str::slug($request->get('titre')), $request->input('id', 0)))
                    return redirect('admin/articles/edit/' . $request->input('id'))->withInput()->withErrors("un article ayant le même titre existe");
                $article = Article::create($request->all());
            }

            return redirect('admin/articles/edit/' . $article->id)->with('success', 1);

        }
    }

    public function getDestroy($id) {
        Article::destroy($id);
        return redirect('admin/articles/index/');
    }

    //CATEGORIES
    public function getCategories($id = 0) {
        $categorie = new Categorie();
        if ($id != 0)
            $categorie = Categorie::find($id);
        $categories = Categorie::all();

        return view('admin.article.categorie')->with('categorie', $categorie)->with('categories', $categories);
    }

    public function getCategorie($id = 0) {
        $categorie = Categorie::find($id);
        $categories = Categorie::all();
        if ($categorie == null)
            $categorie = new Categorie();
        return view('admin.article.categorie')->with('categorie', $categorie)->with('categories', $categories);
    }

    public function postCreateCategorie(Request $request) {
        if ($request->isMethod('post')) {
            Categorie::create($request->all());
            return redirect('admin/articles/categories');
        }
    }

    public function putUpdateCategorie(Request $request) {
        if ($request->isMethod('put')) {
            $id = $request->input('id');
            Categorie::find($id)->update($request->all());
            return redirect('admin/articles/categories');
        }
    }

    public function getCategorieDelete($id, Request $request) {
        $ids = $request->input('delete_cats', $id);
        Categorie::destroy($ids);
        return redirect('admin/articles/categories');
    }

    public function postCategorieAction(Request $request) {
        $action = $request->input('action');
        if ($action == 'delete')
            Categorie::destroy($request->input('id'));
        return redirect('admin/articles/categories');
    }
}