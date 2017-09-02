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
            if($likeDislike->is_like == $request->is_like) {
                $likeDislike->delete($likeDislike->id);
                $movieLikes = $this->movieRepository->findNumberOfLikesDislikes($request->input('movie_id'));
                return response()->json(new JsonResponse([
                    'success' => true,
                    'like_dislike' => [
                        'movie_id' => $movie->id,
                        'user_id' => $user->id,
                        'like'    => false,
                        'dislike' => false,
                        'likes' => $movieLikes['likes'],
                        'dislikes' => $movieLikes['dislikes']
                    ]
                ]));
            }
            else{
                $likeDislike = $this->likeDislikeRepository->save(['is_like' => $request->is_like], $likeDislike->id);
            }

        }
        catch(ModelNotFoundException $e){
            $likeDislike = $this->likeDislikeRepository->save([
                'user_id'  => $user->id,
                'movie_id' => $movie->id,
                'is_like'  => $request->is_like
            ]);
        }

        if($likeDislike){
            $like       = $likeDislike->is_like ? true : false;
            $disLike    = $likeDislike->is_like ? false : true;
            $movieLikes = $this->movieRepository->findNumberOfLikesDislikes($request->input('movie_id'));

            return response()->json(new JsonResponse([
                'success'      => true,
                'like_dislike' => array_merge($likeDislike->getAttributes(), [
                    'likes'    => $movieLikes['likes'],
                    'dislikes' => $movieLikes['dislikes'],
                    'like'     => $like,
                    'dislike'  => $disLike
                ])
            ]));
        }
        else{
            return response()->json(new JsonResponse(['success' => false, 'like_dislike' => null]), 400);
        }
    }
}
