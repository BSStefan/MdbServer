<?php

//Admin
Route::group(['prefix' => 'admin'], function (){
    Route::group(['prefix' => 'tmdb'], function (){
        Route::get('popular-people/{page}', 'Admin\PeopleController@getPopularPeopleFromTmdb')->name('admin.tmdb.popular-people');
        Route::get('genres', 'Admin\GenreController@getAllGenresFromTmdb')->name('admin.tmdb.genres');
        Route::get('popular-movies/{pages}', 'Admin\MovieController@getTopMoviesFromTmdb')->name('admin.tmdb.popular-movies');
        Route::get('popular-movie/{id}', 'Admin\MovieController@getMovieFromTmdb')->name('admin.tmdb.popular-movie');
        Route::get('now-playing-movies', 'Admin\MovieController@getNewestFromTmdb')->name('admin.tmdb.now-playing-movies');
        Route::get('upcoming-movies', 'Admin\MovieController@getUpcomingFromTmdb')->name('admin.tmdb.upcoming-movies');
        Route::get('get-images/{page}', 'Admin\StartController@getTopImage');
    });

    Route::group(['prefix' => 'croler'], function (){
        Route::get('current-movies', 'Admin\MovieController@findCurrentMoviesInCinema')->name('admin.crawler.current-movies');
        Route::get('current-movies-time', 'Admin\CinemaMovieSearchController@findTimeCurrentMoviesInCinema')->name('admin.crawler.time-current-movies');
    });
});



