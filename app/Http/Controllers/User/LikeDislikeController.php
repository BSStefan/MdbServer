<?php

namespace App\Http\Controllers\User;

use App\Helpers\FindSimilarlyMovies;
use App\Helpers\FormatCoefficients;
use App\Helpers\FormatMarks;
use App\Http\Response\JsonResponse;
use App\Models\UserRecommendation;
use App\Repositories\LikeDislikeRepository;
use App\Repositories\MovieModelRepository;
use App\Repositories\MovieRepository;
use App\Repositories\UserCoefficientRepository;
use App\Repositories\UserRecommendationRepository;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Tymon\JWTAuth\Facades\JWTAuth;
use App\Models\User;

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

    private $userCoefficientRepository;
    private $movieModelRepository;
    private $userRecommendationRepository;


    public function __construct(
        LikeDislikeRepository $likeDislikeRepository,
        MovieRepository $movieRepository,
        UserCoefficientRepository $userCoefficientRepository,
        MovieModelRepository $movieModelRepository,
        UserRecommendationRepository $userRecommendationRepository
    )
    {
        $this->likeDislikeRepository        = $likeDislikeRepository;
        $this->movieRepository              = $movieRepository;
        $this->userRecommendationRepository = $userRecommendationRepository;
        $this->movieModelRepository         = $movieModelRepository;
        $this->userCoefficientRepository    = $userCoefficientRepository;
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
        if($likeDislike) {
            $this->createUserCoefficients($user);
            if($likeDislike->is_like){
                $this->likedMovieFindSimilar($likeDislike->movie_id, $user);
            }
            else {
                $this->dislikeMovieFindSimilar($likeDislike->movie_id, $user);
            }
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

    /**
     * Update user recommendation
     *
     * @param int $id
     * @param User $user
     *
     * @return UserRecommendation
     */
    private function likedMovieFindSimilar($id, $user)
    {
        $movieModel = $this->movieModelRepository->findBy('movie_id', $id);
        $otherMovieModels = $this->movieModelRepository->getOthersExceptLikedAndDislikedWatched($user->likes, $user->watched);
        $coefficients = $this->userCoefficientRepository->findBy('user_id', $user->id);
        $similarMovies = FindSimilarlyMovies::findSimilarMovies($movieModel, $otherMovieModels, $coefficients);
        $userRecommendation = $this->userRecommendationRepository->findBy('user_id', $user->id);

        $newRecommendation = FormatMarks::formatFromMultipleArrays([$similarMovies, $userRecommendation->movies]);

        return $this->userRecommendationRepository
            ->saveNewRecommendation($user->id, $newRecommendation, $userRecommendation->last_movie_calculated, $userRecommendation->id);
    }

    /**
     * Update user recommendation
     *
     * @param int $id
     * @param User $user
     *
     * @return UserRecommendation
     */
    private function dislikeMovieFindSimilar($id, $user)
    {
        $movieModel = $this->movieModelRepository->findBy('movie_id', $id);
        $otherMovieModels = $this->movieModelRepository->getOthersExceptLikedAndDislikedWatched($user->likes, $user->watched);
        $coefficients = $this->userCoefficientRepository->findBy('user_id', $user->id);
        $similarMovies = FindSimilarlyMovies::findSimilarMovies($movieModel, $otherMovieModels, $coefficients);
        $userRecommendation = $this->userRecommendationRepository->findBy('user_id', $user->id);
        $newRecommendation = FormatMarks::formatDislikeFromMultipleArrays($userRecommendation->movies, $similarMovies, $movieModel);

        return $this->userRecommendationRepository
            ->saveNewRecommendation($user->id, $newRecommendation, $userRecommendation->last_movie_calculated, $userRecommendation->id);
    }

    /**
     * Update user coefficients
     *
     * @param User $user
     *
     * @return UserRecommendation
     */
    private function createUserCoefficients($user)
    {
        $likedDislikedIds = [];
        foreach($user->likes as $one) {
            array_push($likedDislikedIds, $one->movie_id);
        }
        $userCoefficients = $user->coefficients;
        $likedDisliked = $this->movieModelRepository->findInArray($likedDislikedIds);

        $newCoefficientResponse = FormatCoefficients::formatUserCoefficients($likedDisliked, $userCoefficients);
        if($newCoefficientResponse[0]) {
            $this->userCoefficientRepository->save($newCoefficientResponse[1], $userCoefficients->id);
        }
        return true;
    }

    //private function test($user){
    //    $recommendation = $user->recommendation;
    //
    //    dd(Carbon::createFromFormat('Y-m-d H:i:s',$recommendation->last_updated)->addDays(2)->greaterThan(Carbon::now()));
    //
    //}
}
