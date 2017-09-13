<?php

namespace App\Http\Controllers\User;

use App\Helpers\FindSimilarlyMovies;
use App\Helpers\FormatMarks;
use App\Http\Response\JsonResponse;
use App\Repositories\MovieModelRepository;
use App\Repositories\UserCoefficientRepository;
use App\Repositories\UserProviderRepository;
use App\Repositories\UserRecommendationRepository;
use App\Repositories\UserRepository;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Auth;
use Laravel\Socialite\Facades\Socialite;
use Tymon\JWTAuth\Exceptions\JWTException;
use Tymon\JWTAuth\Exceptions\TokenExpiredException;
use Tymon\JWTAuth\Exceptions\TokenInvalidException;
use Tymon\JWTAuth\JWTAuth;
use Tymon\JWTAuth\Manager;
use Tymon\JWTAuth\Facades\JWTAuth as JWTAuthFacade;

class AuthController extends Controller
{
    /**
     * @var UserRepository
     */
    private $userRepository;

    /**
     * @var UserProviderRepository
     */
    private $userProviderRepository;

    /**
     * @var JWTAuth
     */
    private $jwt;

    private $userCoefficientRepository;

    public function __construct(
        UserRepository $userRepository,
        UserProviderRepository $userProviderRepository,
        JWTAuth $JWTAuth,
        UserCoefficientRepository $userCoefficientRepository
    )
    {
        $this->userRepository            = $userRepository;
        $this->userProviderRepository    = $userProviderRepository;
        $this->jwt                       = $JWTAuth;
        $this->userCoefficientRepository = $userCoefficientRepository;
    }

    /**
     * Redirect the user to the GitHub authentication page.
     *
     * @return array
     */
    public function redirectToProvider($provider)
    {
        return ['redirect_url' => Socialite::driver($provider)->stateless()->redirect()->getTargetUrl()];
    }

    /**
     * Obtain the user information from Facebook.
     *
     * @return JsonResponse
     */
    public function handleProviderCallback($providerName)
    {
        $socialUser = Socialite::driver($providerName)->stateless()->user();
        try{
            $provider = $this->userProviderRepository->findBy('provider_id', $socialUser->id);
            $user     = $this->userRepository->find($provider->user_id);
        }
        catch(ModelNotFoundException $exception){
            $provider = null;
            $user     = null;
        }
        if(!$user){
            list($name, $lastname) = explode(' ', $socialUser->name);
            try{
                $emailUser = $this->userRepository->findBy('email', $socialUser->email);
                $token = $this->jwt->fromUser($emailUser);

                return response()->json(new JsonResponse(['success' => true, 'token' => $token]));
            }
            catch(ModelNotFoundException $e){}
            $user     = $this->userRepository->save([
                'email'      => $socialUser->email,
                'first_name' => $name,
                'last_name'  => $lastname,
                'gender'     => $socialUser->user['gender']
            ]);
            $provider = $this->userProviderRepository->save([
                'user_id'     => $user->id,
                'provider'    => $providerName,
                'provider_id' => $socialUser->id
            ]);
        }
        if($user){
            $token = $this->jwt->fromUser($user);

            return response()->json(new JsonResponse(['success' => true, 'token' => $token]));
        }

        return response()->json(new JsonResponse(['success' => false, 'token' => null]));
    }

    /**
     * Login user from credential
     *
     * @param Request $request
     * @param Manager $manager
     *
     * @return JsonResponse
     */
    public function loginUser(Request $request, Manager $manager)
    {
        $this->validate($request, [
            'email'    => 'required|email',
            'password' => 'required|min:6|max:255'
        ]);
        try{
            if(!$token = $this->jwt->attempt($request->only('email', 'password'))){
                return response()->json(new JsonResponse([
                    'success' => false, 'token' => null
                ], 'Bad credentials', 200, true), 200);
            }
        }
        catch(TokenExpiredException $e){
            $token = $manager->refresh(JWTAuthFacade::getToken())->get();
        }
        catch(JWTException $e){
            return response()->json(new JsonResponse([
                'success' => false, 'token' => null
            ], $e->getMessage(), 401), 401);
        }

        $user = JWTAuthFacade::user();
        if(!$user->is_admin){
            $this->checkRecommendation($user);
        }
        return response()->json(new JsonResponse([
            'success'    => true,
            'token'      => $token,
            'first_name' => $user->first_name,
            'last_name'  => $user->last_name,
            'is_admin'   => $user->is_admin
        ]));
    }

    /**
     * Register user
     * @param Request $request
     *
     * @return JsonResponse
     */
    public function registerUser(Request $request)
    {
        $this->validate($request, [
            'email'      => 'required|email|unique:users',
            'first_name' => 'required|min:2|max:50',
            'last_name'  => 'required|min:2|max:50',
            'gender'     => 'nullable',
            'password'   => 'required|min:6|max:255',
            'birthday'   => 'nullable|date',
            'city'       => 'nullable|string'
        ]);

        $user = $this->userRepository->save([
            'email'      => $request->email,
            'first_name' => $request->first_name,
            'last_name'  => $request->last_name,
            'gender'     => $request->gender,
            'birthday'   => $request->birthday,
            'password'   => $request->password,
            'city'       => $request->city
        ]);

        if($user){
            $token = $this->jwt->fromUser($user);
            $coefficients = $this->userCoefficientRepository->setToDefault($user->id);

            return response()->json(new JsonResponse([
                'success' => true,
                'token' => $token,
                'first_name' => $user->first_name,
                'last_name' => $user->last_name,
                'is_admin' => $user->is_admin
            ]));
        }
        else{
            return response()->json(new JsonResponse([
                'success'    => false,
                'token'      => null,
                'first_name' => null,
                'last_name'  => null,
                'is_admin'   => null
            ], 'User is not registered', 200, true));
        }
    }

    /**
     * Check if email exists
     * @param Request $request
     *
     * @return JsonResponse
     */
    public function checkEmailExists(Request $request)
    {
        $this->validate($request, [
            'email' => 'email'
        ]);

        try{
            $user = $this->userRepository->findBy('email', $request->email);
        }
        catch(ModelNotFoundException $e){
            $user = null;
        }

        if($user){
            return response()->json(new JsonResponse(
                ['success' => false],
                'User already exists',
                200
            ));
        }

        return response()->json(new JsonResponse(
            ['success' => true],
            '',
            200
        ));
    }

    /**
     * Logout User
     *
     * @return JsonResponse
     */
    public function logoutUser()
    {
        if($this->jwt->manager()->invalidate($this->jwt->getToken())){
            return response()->json(new JsonResponse([
                'success' => true
            ]));
        }
        return response()->json(new JsonResponse([
            'success' => false
        ], '',400),400);
    }

    private function checkRecommendation($user)
    {
        $userRecommendation = $user->recommendation;
        $movieModelRepository = resolve(MovieModelRepository::class);
        $userRecommendationRepository = resolve(UserRecommendationRepository::class);
        $lastMovieId = intval($movieModelRepository->findLast()->movie_id);
        if(
            ($lastMovieId-intval($userRecommendation->last_movie_calculated)) > 5
            || Carbon::createFromFormat('Y-m-d H:i:s', $userRecommendation->last_updated)->addDays(2)->lessThan(Carbon::now())
        ) {
            $likedMovies = [];
            $liked = $user->onlyLiked;
            foreach($liked as $one){
                array_push($likedMovies, $one->movie_id);
            }
            $dislikedMovies = [];
            $disliked = $user->onlyDisliked;
            foreach($disliked as $one){
                array_push($dislikedMovies, $one->movie_id);
            }
            $watchedMovies = [];
            $watched = $user->watched;
            foreach($watched as $one){
                array_push($watchedMovies, $one->movie_id);
            }
            $otherMovies = $movieModelRepository->getNotInArray(array_merge($likedMovies, $dislikedMovies, $watchedMovies));
            $similarityLiked = [];
            foreach($likedMovies as $movieId) {
                $movieModel = $movieModelRepository->findBy('movie_id',$movieId);
                $similarMovies = FindSimilarlyMovies::findSimilarMovies($movieModel, $otherMovies, $user->coefficients);
                $similarityLiked[$movieId] = $similarMovies;
            }
            $similarityLiked = FormatMarks::formatFromMultipleArrays($similarityLiked);
            $similarityDisliked = [];
            foreach($dislikedMovies as $movieId) {
                $movieModel = $movieModelRepository->findBy('movie_id',$movieId);
                $similarMovies = FindSimilarlyMovies::findSimilarMovies($movieModel, $otherMovies, $user->coefficients);
                $similarityDisliked[$movieId] = $similarMovies;
            }
            $similarityDisliked = FormatMarks::formatFromMultipleArrays($similarityDisliked);

            $new = FormatMarks::formatLikeDislikeUpdateAll($similarityLiked, $similarityDisliked);
            $updated =$userRecommendationRepository->saveNewRecommendation($user->id, $new, $lastMovieId,$userRecommendation->id, true);
            return $updated;
        }
        return $userRecommendation;

    }


        //$coefficients = $userCoefficientRepository->findBy('user_id', $user->id);
        //$otherMovies = $movieModelRepository->getNotInArray($moviesArray);
        //$similarity = [];
        //foreach($moviesArray as $movieId) {
        //$movieModel = $movieModelRepository->findBy('movie_id',$movieId);
        //$similarMovies = FindSimilarlyMovies::findSimilarMovies($movieModel, $otherMovies, $coefficients);
        //$similarity[$movieId] = $similarMovies;
        //}
        //
        //$similarity = FormatMarks::formatFromMultipleArrays($similarity);
        //$lastMovie = $movieModelRepository->findLast();
        //
        //return $userRecommendationRepository->saveNewRecommendation($user->id, $similarity, $lastMovie->movie_id);
}
