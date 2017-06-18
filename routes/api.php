<?php

//Admin

Route::group(['prefix' => 'tmdb'], function(){
    Route::get('popular-people/{page}','Admin\PeopleController@getPopularPeopleFromTmdb')->name('tmdb.popular.people');
    Route::get('genres','Admin\GenreController@getAllGenresFromTmdb')->name('tmdb.genres');
    Route::get('popular-movies/{pages}','Admin\MovieController@getTopMoviesFromTmdb')->name('tmdb.popular.movies');
    Route::get('popular-movie/{id}','Admin\MovieController@getMovieFromTmdb')->name('tmdb.popular.movie');
    Route::get('now-playing-movies','Admin\MovieController@getNewestFromTmdb')->name('tmdb.now-playing.movies');
    Route::get('upcoming-movies','Admin\MovieController@getUpcomingFromTmdb')->name('tmdb.upcoming.movies');
    Route::get('get-images/{page}', 'Admin\StartController@getTopImage');
});

