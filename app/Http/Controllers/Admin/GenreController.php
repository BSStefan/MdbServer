<?php


namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Repositories\GenreRepository;
use App\Repositories\Admin\TmdbRepository;

class GenreController extends Controller
{
    private $tmdbRepository,
            $genreRepository;

    public function __construct(
        TmdbRepository $tmdbRepository,
        GenreRepository $genreRepository
    )
    {
        $this->tmdbRepository = $tmdbRepository;
        $this->genreRepository = $genreRepository;
    }

    public function getAllGenresFromTmdb()
    {
        $list = $this->tmdbRepository->getGenres();

        foreach ($list as $item) {
            $this->genreRepository->save(['name' => $item]);
        }

        return response()->json('The genres were successfully saved.', 200);
    }
}