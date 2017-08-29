<?php

namespace App\Http\Controllers\User;

use App\Http\Response\JsonResponse;
use App\Repositories\WriterRepository;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class WriterController extends Controller
{
    private $writerRepository;

    public function __construct(
        WriterRepository $writerRepository
    )
    {
        $this->writerRepository = $writerRepository;
    }

    public function getWriterWithDetails($id)
    {
        $writerWithDetails = $this->writerRepository->getWriterWithDetails($id);

        return response()->json(new JsonResponse($writerWithDetails));
    }
}
