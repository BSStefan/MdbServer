<?php

namespace App\Http\Controllers\Admin;

use App\Repositories\ActorRepository;
use App\Repositories\Admin\TmdbRepository;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class ActorController extends Controller
{
    private $actorRepository,
            $tmdbRepository;

    public function __construct(
        ActorRepository $actorRepository,
        TmdbRepository $tmdbRepository
    )
    {
        $this->actorRepository = $actorRepository;
        $this->tmdbRepository = $tmdbRepository;
    }
}
