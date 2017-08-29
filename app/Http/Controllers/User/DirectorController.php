<?php

namespace App\Http\Controllers\User;

use App\Http\Response\JsonResponse;
use App\Repositories\DirectorRepository;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class DirectorController extends Controller
{
    private $directorRepository;

    public function __construct(
        DirectorRepository $directorRepository
    )
    {
        $this->directorRepository = $directorRepository;
    }

    public function getDirectorWithDetails($id)
    {
        $directorWithDetails = $this->directorRepository->getDirectorWithDetails($id);

        return response()->json(new JsonResponse($directorWithDetails));
    }
}
