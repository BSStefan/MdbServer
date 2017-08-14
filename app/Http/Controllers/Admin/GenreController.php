<?php


namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Repositories\GenreRepository;
use App\Repositories\Admin\TmdbRepository;

class GenreController extends Controller
{
    /**
     * @var TmdbRepository $tmdbRepository
     */
    private $tmdbRepository;

    /**
     * @var GenreRepository $genreRepository
     */
    private $genreRepository;

    /**
     * @param TmdbRepository $tmdbRepository
     * @param GenreRepository $genreRepository
     */
    public function __construct(
        TmdbRepository $tmdbRepository,
        GenreRepository $genreRepository
    )
    {
        $this->tmdbRepository = $tmdbRepository;
        $this->genreRepository = $genreRepository;
    }

    /**
     * Save all genres in db
     */
    public function getAllGenresFromTmdb()
    {
        $list = $this->tmdbRepository->getGenres();

        return response()->json([
            'status'  => $this->genreRepository->saveAllGenres($list),
        ], 200);
    }
}