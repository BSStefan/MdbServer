<?php

namespace App\Http\Controllers\User;

use App\Http\Response\JsonResponse;
use App\Repositories\LikeDislikeRepository;
use App\Repositories\MovieRepository;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Tymon\JWTAuth\Facades\JWTAuth;

class LikeDislikeController extends Controller
{
    /**
     * @var LikeDislikeRepository $likeDislikeRepository
     */
    private $likeDislikeRepository;

    /**
     * @var MovieRepository $movieRepository
     */
    private $movieRepository;

    public function __construct(
        LikeDislikeRepository $likeDislikeRepository,
        MovieRepository $movieRepository
    )
    {
        $this->likeDislikeRepository = $likeDislikeRepository;
        $this->movieRepository = $movieRepository;
    }

    public function likeDislikeMovie(Request $request)
    {
        $user  = JWTAuth::user();
        try{
            $movie = $this->movieRepository->find($request->input('movie_id'));
        }
        catch(\Exception $e){
            return response()->json(new JsonResponse(['success' => false, 'like_dislike' => null], 'Movie not found', 400), 400);
        }

        $this->validate($request, [
            'movie_id' => 'required|integer',
            'is_like' => 'required|boolean'
        ]);

        try{
            $likeDislike = $this->likeDislikeRepository->checkIfUserAlreadyLikedDislikedMovie($user->id, $movie->id);
            $likeDislike = $this->likeDislikeRepository->save(['is_like' => $request->is_like], $likeDislike->id);
        }
        catch(ModelNotFoundException $e){
            $likeDislike = null;
            $likeDislike = $this->likeDislikeRepository->save([
                'user_id'  => $user->id,
                'movie_id' => $movie->id,
                'is_like'  => $request->is_like
            ]);
        }

        if($likeDislike){
            return response()->json(new JsonResponse(['success' => true, 'like_dislike' => $likeDislike]));
        }
        else{
            return response()->json(new JsonResponse(['success' => false, 'like_dislike' => null]), 400);
        }
    }
}
