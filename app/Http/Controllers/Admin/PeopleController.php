<?php

namespace App\Http\Controllers\Admin;

use App\Repositories\ActorRepository;
use App\Repositories\DirectorRepository;
use App\Repositories\Admin\TmdbRepository;
use App\Http\Controllers\Controller;
use App\Repositories\WriterRepository;
use Psy\Util\Json;

class PeopleController extends Controller
{
    /**
     * @var TmdbRepository $tmdbRepository
     */
    private $tmdbRepository;

    /**
     * @var ActorRepository $actorRepository
     */
    private $actorRepository;

    /**
     * @var DirectorRepository $directorRepository
     */
    private $directorRepository;

    /**
     * @var WriterRepository $writerRepository
     */
    private $writerRepository;

    /**
     * @param ActorRepository $actorRepository
     * @param TmdbRepository $tmdbRepository
     * @param DirectorRepository $directorRepository
     * @param WriterRepository $writerRepository
     */
    public function __construct(
        ActorRepository $actorRepository,
        TmdbRepository $tmdbRepository,
        DirectorRepository $directorRepository,
        WriterRepository $writerRepository
    )
    {
        $this->actorRepository    = $actorRepository;
        $this->tmdbRepository     = $tmdbRepository;
        $this->directorRepository = $directorRepository;
        $this->writerRepository   = $writerRepository;
    }

    /**
     * @param int $page
     * @return Json
     */
    public function getPopularPeopleFromTmdb($page)
    {
        //TODO image factory problem
        $people = $this->tmdbRepository->getPopularPeople($page);

        foreach($people as $person){
            $this->savePersonPerRole($person);
        }

        return response()->json([
            'success' => true,
        ],
            200);
    }

    /**
     * @param int $id
     * @return Json
     */
    public function getPersonFromTmdb($id)
    {
        $person   = $this->tmdbRepository->getPerson($id);
        $response = $this->savePersonPerRole($person) ? true : false;

        return response()->json([
            'success' => $response,
        ], 200);
    }

    /**
     * Function for saving people per role in db
     * @param array $person
     * @return mixed
     */
    private function savePersonPerRole($person){
        switch ($person['role']){
            case 'actor':
                $person['image_url'] = $person['image_url'] ?
                    $this->saveImageFromUrl($person['image_url'], 'images/actors') : 'No image';
                unset($person['role']);
                return $this->actorRepository->save($person);
            case 'director':
                $person['image_url'] = $person['image_url'] ?
                    $this->saveImageFromUrl($person['image_url'], 'images/directors') : 'No image';
                unset($person['role']);
                return $this->directorRepository->save($person);
            case 'writer':
                $person['image_url'] = $person['image_url'] ?
                    $this->saveImageFromUrl($person['image_url'], 'images/writers') : 'No image';
                unset($person['role']);
                return $this->writerRepository->save($person);
            default:
                return null;
        }
    }
}
