<?php

namespace App\Http\Controllers;

use App\Http\Response\JsonResponse;
use App\Models\Director;
use App\Models\Movie;
use App\Models\Writer;
use App\Repositories\ActorRepository;
use App\Repositories\Admin\CrawlerRepository;
use App\Repositories\Admin\TmdbRepository;
use App\Repositories\DirectorRepository;
use App\Repositories\GenreRepository;
use App\Repositories\KeywordRepository;
use App\Repositories\MovieRepository;
use App\Repositories\WriterRepository;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Tmdb\Client;
use Tmdb\Repository\SearchRepository;
use App\Repositories\Eloquent\Repository;

class SeederController extends Controller
{
    /**
     * @var TmdbRepository $tmdbRepository
     */
    private $tmdbRepository;

    /**
     *@var MovieRepository $movieRepository
     */
    private $movieRepository;

    /**
     * @var DirectorRepository $directorRepository
     */
    private $directorRepository;
    private $actorRepository;
    private $writerRepository;
    private $genreRepository;
    private $keywordRepository;
    private $crawlerRepository;

    public function __construct(
        TmdbRepository $tmdbRepository,
        MovieRepository $movieRepository,
        DirectorRepository $directorRepository,
        ActorRepository $actorRepository,
        WriterRepository $writerRepository,
        GenreRepository $genreRepository,
        KeywordRepository $keywordRepository,
        CrawlerRepository $crawlerRepository
    )
    {
        $this->tmdbRepository     = $tmdbRepository;
        $this->movieRepository    = $movieRepository;
        $this->directorRepository = $directorRepository;
        $this->actorRepository    = $actorRepository;
        $this->writerRepository   = $writerRepository;
        $this->genreRepository    = $genreRepository;
        $this->keywordRepository  = $keywordRepository;
        $this->crawlerRepository  = $crawlerRepository;
    }

    public function getInfo()
    {
        $movies = $this->movieRepository->all();
        foreach($movies as $movie)
        {
            echo '['.$movie->id.','.$movie->tmdb_id.', "'.$movie->director_id.', "'.$movie->title.'", "'.$movie->original_title.'", "'.$movie->budget.'", "'.$movie->homepage.'", "'.$movie->description.'", "'.$movie->language.'", "'.$movie->tag_line.'", "'.$movie->release_day.'", "'.$movie->runtime.'", "'.$movie->image_url.'", "'.$movie->in_cinema.'" ],'. "\n";
        }

    }

}
