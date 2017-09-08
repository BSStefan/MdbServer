<?php

namespace App\Http\Controllers;

use App\Models\MovieModel;
use App\Models\UserRecommendation;
use App\Repositories\LikeDislikeRepository;
use App\Repositories\MovieModelRepository;
use App\Repositories\UserCoefficientRepository;
use App\Repositories\UserRecommendationRepository;
use Illuminate\Foundation\Bus\DispatchesJobs;
use Illuminate\Routing\Controller as BaseController;
use Illuminate\Foundation\Validation\ValidatesRequests;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Support\Facades\Storage;
use Intervention\Image\Facades\Image;
use Symfony\Component\HttpFoundation\File\File;
use Illuminate\Http\Request;
use Tymon\JWTAuth\Facades\JWTAuth;
use App\Helpers\FormatMarks;
use App\Helpers\FindSimilarlyMovies;


class Controller extends BaseController
{
    use AuthorizesRequests, DispatchesJobs, ValidatesRequests;

    public function saveImageFromUrl($url, $path)
    {
        $extension = pathinfo($url,PATHINFO_EXTENSION);
        $fullName =  $path . '/' . md5(microtime()) . '.' . $extension;
        try{
            $image = Image::make($url)->encode('jpg', 60)->resize(800,1200);
            $image->save(public_path($fullName));
            $saved_image_uri = $image->dirname.'/'.$image->basename;
            $uploaded_thumbnail_image = Storage::putFileAs('public/', new File($saved_image_uri), $fullName);
            $image->destroy();
            unlink($saved_image_uri);
        }
        catch(\Exception $e){
            $fullName = 'No image';
        }

        return $fullName;
    }

    ////public function userModelFirst(
    ////    array $moviesArray
    ////)
    ////{
    ////    $userCoefficientRepository = resolve(UserCoefficientRepository::class);
    ////    $movieModelRepository = resolve(MovieModelRepository::class);
    ////    $userRecommendationRepository = resolve(UserRecommendationRepository::class);
    ////    $user  = JWTAuth::user();
    ////    $coefficients = $userCoefficientRepository->findBy('user_id', $user->id);
    ////    $otherMovies = $movieModelRepository->getNotInArray($moviesArray);
    ////    $similarity = [];
    ////    foreach($moviesArray as $movieId) {
    ////        $movieModel = $movieModelRepository->findBy('movie_id',$movieId);
    ////        $similarMovies = FindSimilarlyMovies::findSimilarMovies($movieModel, $otherMovies, $coefficients);
    ////        $similarity[$movieId] = $similarMovies;
    ////    }
    ////
    ////    $similarity = FormatMarks::formatFromMultipleArrays($similarity);
    ////    $lastMovie = $movieModelRepository->findLast();
    ////
    ////    return $userRecommendationRepository->saveNewRecommendation($user->id, $similarity, $lastMovie->movie_id);
    ////}
    //
    //
    //
    //
    //
    //
    ////protected function likedMovieFindSimilar(
    ////    $id,
    ////    UserCoefficientRepository $userCoefficientRepository,
    ////    MovieModelRepository $movieModelRepository,
    ////    UserRecommendationRepository $userRecommendationRepository
    ////)
    ////{
    ////    $user = JWTAuth::user();
    ////    $movieModel = $movieModelRepository->findBy('movie_id', $id);
    ////    $otherMovieModels = $movieModelRepository->getAllOthers($id);
    ////    $coefficients = $userCoefficientRepository->findBy('user_id', $user->id);
    ////    $similarMovies = $this->findSimilarMovies($movieModel, $otherMovieModels, $coefficients);
    ////    $userRecommendation = $userRecommendationRepository->findBy('user_id', $user->id);
    ////
    ////    $newRecommendation = FormatMarks::formatFromMultipleArrays([$similarMovies,$userRecommendation->movies]);
    ////
    ////    $userRecommendationRepository->saveNewRecommendation($user->id, $newRecommendation, $userRecommendation->last_movie_calculated, $userRecommendation->id);
    ////}
    //
    //protected function updateSimilar(
    //    LikeDislikeRepository $likeDislikeRepository,
    //    UserCoefficientRepository $userCoefficientRepository,
    //    MovieModelRepository $movieModelRepository,
    //    UserRecommendationRepository $userRecommendationRepository
    //)
    //{
    //    $user = JWTAuth::user();
    //    $likedMovies = $likeDislikeRepository->getAllLikes($user->id);
    //    $coefficients = $userCoefficientRepository->findBy('user_id', $user->id);
    //    $otherMovies = $movieModelRepository->getNotInArray($likedMovies);
    //    $similarity = [];
    //    foreach($likedMovies as $movieId) {
    //        $movieModel = $movieModelRepository->findBy('movie_id',$movieId);
    //        $similarMovies = $this->findSimilarMovies($movieModel, $otherMovies, $coefficients);
    //        $similarity[$movieId] = $similarMovies;
    //    }
    //    $old = $userRecommendationRepository->findBy('user_id', $user->id);
    //    $similarity = FormatMarks::formatFromMultipleArrays($similarity);
    //    $lastMovie = $movieModelRepository->findLast();
    //
    //    $userRecommendationRepository->saveNewRecommendation($user->id, $similarity, $lastMovie->movie_id, $old->id);
    //}
}

