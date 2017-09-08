<?php

//Admin
Route::group(['middleware' => 'cors'], function (){
    Route::group(['prefix' => 'admin', 'middleware' => 'auth.api.admin'], function (){
        Route::group(['prefix' => 'tmdb'], function (){
            Route::post('popular-people/{page}', 'Admin\PeopleController@postPopularPeopleFromTmdb')->name('admin.tmdb.popular-people');
            Route::post('genres', 'Admin\GenreController@getAllGenresFromTmdb')->name('admin.tmdb.genres');
            Route::get('popular-movies/{page}', 'Admin\MovieController@getTopMoviesFromTmdb')->name('admin.tmdb.popular-movies');
            Route::post('popular-movies/{page}', 'Admin\MovieController@postTopMoviesFromTmdb')->name('admin.tmdb.popular-movies');
            Route::post('movie/{id}', 'Admin\MovieController@postMovieFromTmdb')->name('admin.tmdb.movie');
            Route::post('movies', 'Admin\MovieController@postMultipleMoviesFromTmdb')->name('admin.tmdb.movies');
            Route::post('new-movies/{page}', 'Admin\MovieController@postNewestFromTmdb')->name('admin.tmdb.now-playing-movies');
            Route::get('new-movies/{page}', 'Admin\MovieController@getNewestFromTmdb')->name('admin.tmdb.now-playing-movies');
            Route::post('upcoming-movies/{page}', 'Admin\MovieController@postUpcomingFromTmdb')->name('admin.tmdb.upcoming-movies');
            Route::get('upcoming-movies/{page}', 'Admin\MovieController@getUpcomingFromTmdb')->name('admin.tmdb.upcoming-movies');
            Route::get('get-images/{page}', 'Admin\StartController@getTopImage');
        });
        Route::get('info', 'Admin\StartController@getInfo');
        Route::group(['prefix' => 'crawler'], function (){
            Route::get('current-in-cinema/{page}', 'Admin\MovieController@getCurrentMoviesInCinema')->name('admin.crawler.current-movies-show');
            Route::post('current-movies', 'Admin\MovieController@findCurrentMoviesInCinema')->name('admin.crawler.current-movies');
            Route::post('current-movie', 'Admin\MovieController@addCurrentMovieInCinema')->name('admin.crawler.current-movie');
            Route::post('current-movies-time', 'Admin\CinemaMovieSearchController@findTimeCurrentMoviesInCinema')->name('admin.crawler.time-current-movies');
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
    Route::group(['prefix' => 'user', 'middleware' => 'auth.api'], function (){
        Route::get('movie/{id}', 'User\MovieController@getMovie');
        Route::get('movie-cinema/{id}/{city}', 'Admin\CinemaMovieSearchController@getProjections');
        Route::get('genres', 'User\GenreController@getGenres');
        Route::get('per-genre/{id}/{perPage}', 'User\MovieController@getMoviePerGenre');
        Route::post('register-movies', 'Admin\MovieController@registerUserMovies');
        Route::get('current-in-cinema/{perPage}', 'User\MovieController@getCurrentInCinema');
        Route::get('keyword-movies/{id}', 'User\MovieController@getMoviePerKeyword');
        Route::post('like-dislike', 'User\LikeDislikeController@likeDislikeMovie');
        Route::get('like-dislike/{type}/{perPage}', 'User\MovieController@getLikeDislikeMovies');
        Route::get('most-liked/{perPage}', 'User\MovieController@getMostLiked');
        Route::get('recommendation/{perPage}', 'User\MovieController@getRecommendation');
        Route::get('watched-to-be-watched/{type}/{perPage}', 'User\WatchMovieController@getMovies');
        Route::post('watched-to-be-watched', 'User\WatchMovieController@addMovie');
        Route::get('new-movies/{perPage}', 'User\MovieController@getNewMovies');
        Route::get('actor/{id}', 'User\ActorController@getActorWithDetails');
        Route::get('director/{id}', 'User\DirectorController@getDirectorWithDetails');
        Route::get('writer/{id}', 'User\WriterController@getWriterWithDetails');
        Route::group(['prefix' => 'info'],function (){
            Route::get('get', 'User\UserController@getInfo');
            Route::put('update', 'User\UserController@updateInfo');
            Route::put('update-password', 'User\UserController@updatePassword');
        });
        //test rute
        Route::get('test', 'Controller@test1');
        Route::get('like/{id}', 'Controller@likedMovieFindSimilar');
        Route::post('like', 'Controller@test2');
    });
    Route::get('search-movie', 'User\MovieController@getSearchMovie');

    Route::get('image', 'Web\ImageController@getImage');
});


Route::get('test', 'Admin\MovieModelController@setUp');




