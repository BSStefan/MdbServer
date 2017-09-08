<?php

namespace App\Http\Controllers\Admin;

use App\Repositories\MovieModelRepository;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

class MovieModelController extends Controller
{
    private $movieModelRepository;

    public function __construct(
        MovieModelRepository $movieModelRepository
    )
    {
        $this->movieModelRepository = $movieModelRepository;
    }

    public function similarMovies($id)
    {
        $movieModel = $this->movieModelRepository->findBy('movie_id', $id);
        $otherMovieModels = $this->movieModelRepository->getAllOthers($id);
        $similarMovies = [];
        foreach($otherMovieModels as $movie){
            $similarity = $this->findSimilarity($movieModel, $movie);
            if($similarity > 0.25) {
                $similarMovies[$movie->movie_id]=$similarity;
               //array_push($similarMovies, $movie->movie_id);
            }
        }
        //$similarMovies = implode('/',$similarMovies);
        //var_dump($similarMovies); exit;
        return $similarMovies;
    }

    private function findSimilarity($movie1, $movie2)
    {
        $director = 2 * ($movie1->director_id == $movie2->director_id ? 1 : 0);
        $actors =  3 * ($this->findGroupSimilarity($movie1->actors, $movie2->actors));
        $writters = $this->findGroupSimilarity($movie1->writers, $movie2->writers);
        $keywords = $this->findGroupSimilarity($movie1->keywords, $movie2->keywords);
        $genres = $this->findGroupSimilarity($movie1->genres, $movie2->genres);
       // $genres = 0;
        return $actors+$director+$writters+$keywords+$genres;
    }

    public function findGroupSimilarity($firstGroup, $secondGroup)
    {
        $similarity = 0;
        foreach($firstGroup as $one) {
            if(in_array($one, $secondGroup)){
                $similarity+=1;
            }
        }
        return $similarity/count($firstGroup);
    }

    public function setUp()
    {
        $allMoviesModels = $this->movieModelRepository->all();
        $similarity = [];
        foreach($allMoviesModels as $movieModel) {
            array_push($similarity, [
                'movie_id' => $movieModel->movie_id,
                'similar_movie' => $this->similarMovies($movieModel->movie_id),
                'created_at' => Carbon::now()->toDateTimeString(),
                'updated_at' => Carbon::now()->toDateTimeString()
            ]);
        }
        var_dump($similarity);
        //DB::table('similar_movies')->insert($similarity);

    }
}
