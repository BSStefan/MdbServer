<?php

namespace App\Http\Controllers\User;

use App\Http\Response\JsonResponse;
use App\Repositories\ActorRepository;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class ActorController extends Controller
{
    private $actorRepository;

    public function __construct(
        ActorRepository $actorRepository
    )
    {
        $this->actorRepository = $actorRepository;
    }

    public function getActorWithDetails($id)
    {
        $actorWithDetails = $this->actorRepository->getActorDetails($id);

        return response()->json(new JsonResponse($actorWithDetails));
    }
}
