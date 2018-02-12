<?php

Route::group(['namespace' => 'Front'], function () {

    Route::get('/', "HomeController@index");
    Route::get('/nous-contacter', 'HomeController@contact');
    Route::post('/nous-contacter', 'HomeController@contact');
    Route::get('/qui-sommes-nous', 'HomeController@about');
    Route::get('/fonctionnement-de-nos-cours', 'HomeController@fonctionnement');
    Route::get('/conseils', 'HomeController@conseils');


    Route::get('/classes/{niveau?}', 'CoursController@classeOption');

    Route::get('/cours/{matiere?}', 'CoursController@cours');
    Route::get('/cours/{matiere}/{slug}', 'CoursController@cour');
    Route::get('/cours/{matiere}/{cours}/{slug}', 'CoursController@chapitre');
    Route::get('/trouver/cours', 'CoursController@recherche');
    Route::get('/recherche-cours', 'CoursController@recherchecours');

    Route::get('/recherche/etudes', 'EtudeController@recherche');
    Route::get('/etudes/{type?}', 'EtudeController@etudes');
    Route::get('/etudes/{type}/{slug}', 'EtudeController@etude');

    Route::get('/actualites/{categorie?}', 'ActualiteController@actualites');
    Route::get('/actualites/{categorie}/{slug}', 'ActualiteController@actualite');


    Route::get('/corriges', 'CorrigeController@corriges');
    Route::get('/corriges/recherche', 'CorrigeController@recherche');
    Route::get('/corriges/{matiere}/{slug}', 'CorrigeController@corrige');
    Route::get('/corriges/{matiere?}/{examen?}/{session}', 'CorrigeController@corriges');

});
