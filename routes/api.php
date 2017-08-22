<?php

//Admin
Route::group(['prefix' => 'admin'], function (){
    Route::group(['prefix' => 'tmdb'], function (){
        Route::post('popular-people/{page}', 'Admin\PeopleController@postPopularPeopleFromTmdb')->name('admin.tmdb.popular-people');
        Route::post('genres', 'Admin\GenreController@getAllGenresFromTmdb')->name('admin.tmdb.genres');
        Route::get('popular-movies/{page}', 'Admin\MovieController@getTopMoviesFromTmdb')->name('admin.tmdb.popular-movies');
        Route::post('popular-movies/{page}', 'Admin\MovieController@postTopMoviesFromTmdb')->name('admin.tmdb.popular-movies');
        Route::post('movie/{id}', 'Admin\MovieController@postMovieFromTmdb')->name('admin.tmdb.movie');
        Route::post('movies', 'Admin\MovieController@postMultipleMoviesFromTmdb')->name('admin.tmdb.movies');
        Route::post('now-playing-movies/{page}', 'Admin\MovieController@postNewestFromTmdb')->name('admin.tmdb.now-playing-movies');
        Route::get('now-playing-movies/{page}', 'Admin\MovieController@getNewestFromTmdb')->name('admin.tmdb.now-playing-movies');
        Route::post('upcoming-movies/{page}', 'Admin\MovieController@postUpcomingFromTmdb')->name('admin.tmdb.upcoming-movies');
        Route::get('upcoming-movies/{page}', 'Admin\MovieController@getUpcomingFromTmdb')->name('admin.tmdb.upcoming-movies');
        Route::get('get-images/{page}', 'Admin\StartController@getTopImage');
    });
    Route::group(['prefix' => 'crawler'], function (){
        Route::post('current-movies', 'Admin\MovieController@findCurrentMoviesInCinema')->name('admin.crawler.current-movies');
        Route::get('current-movies-time', 'Admin\CinemaMovieSearchController@findTimeCurrentMoviesInCinema')->name('admin.crawler.time-current-movies');
    });
});
Route::group(['prefix' => 'auth'], function (){
    Route::get('{provider}/login', 'User\AuthController@redirectToProvider');
    Route::get('{provider}/callback', 'User\AuthController@handleProviderCallback');
    Route::group(['prefix' => 'mdb'], function (){
        Route::post('login', 'User\AuthController@loginUser')->name('auth.mdb.login');
        Route::post('register', 'User\AuthController@registerUser')->name('auth.mdb.register');
        Route::post('check-email', 'User\AuthController@checkEmailExists')->name('auth.mdb.check-email');
        Route::get('logout', ['uses'=> 'User\AuthController@logoutUser', 'middleware' => 'auth.api'])->name('auth.mdb.logout');
    });
});

Route::get('seeder', 'SeederController@getInfo');





