<?php

namespace App\Http\Controllers\Admin;

use App\Repositories\ActorRepository;
use App\Repositories\DirectorRepository;
use App\Repositories\Admin\TmdbRepository;
use App\Http\Controllers\Controller;
use App\Repositories\WriterRepository;

class PeopleController extends Controller
{
    private $tmdbRepository,
            $actorRepository,
            $directorRepository,
            $writerRepository;

    public function __construct(
        ActorRepository $actorRepository,
        TmdbRepository $tmdbRepository,
        DirectorRepository $directorRepository,
        WriterRepository $writerRepository
    )
    {
        $this->actorRepository = $actorRepository;
        $this->tmdbRepository = $tmdbRepository;
        $this->directorRepository = $directorRepository;
        $this->writerRepository = $writerRepository;
    }

    public function getPopularPeopleFromTmdb($page)
    {
        $people = $this->tmdbRepository->getPopularPeople($page);

        foreach ($people as $person) {
            $this->savePersonPerRole($person);
        }
        return response()->json('Actors are successfully saved', 200);
    }

    public function getPersonFromTmdb($id)
    {
        $person = $this->tmdbRepository->getPerson($id);
        $this->savePersonPerRole($person);
        return response()->json('Actor are successfully saved', 200);
    }

    private function savePersonPerRole($person){
        switch ($person['role']){
            case 'actor':
                $person['image_url'] = $person['image_url'] ?
                    $this->saveImageFromUrl($person['image_url'], 'images/actors') : 'No image';
                return $this->actorRepository->save($person);
            case 'director':
                $person['image_url'] = $person['image_url'] ?
                    $this->saveImageFromUrl($person['image_url'], 'images/directors') : 'No image';
                return $this->directorRepository->save($person);
            case 'writer':
                $person['image_url'] = $person['image_url'] ?
                    $this->saveImageFromUrl($person['image_url'], 'images/writers') : 'No image';
                return $this->writerRepository->save($person);
            default:
                return null;
        }
    }
}
