<?php

namespace App\Http\Controllers\User;

use App\Http\Response\JsonResponse;
use App\Repositories\GenreRepository;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class GenreController extends Controller
{
    private $genreRepository;

    public function __construct(
        GenreRepository $genreRepository
    )
    {
        $this->genreRepository = $genreRepository;
    }

    public function getGenres()
    {
        $genres = $this->genreRepository->all();

        return response()->json(new JsonResponse($genres));
    }
}
