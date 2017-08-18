<?php

namespace App\Http\Controllers\User;

use App\Http\Requests\AuthMdbLoginRequest;
use App\Http\Response\JsonResponse;
use App\Repositories\UserProviderRepository;
use App\Repositories\UserRepository;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Http\Response;
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
    protected $userRepository;

    /**
     * @var UserProviderRepository
     */
    protected $userProviderRepository;

    /**
     * @var JWTAuth
     */
    protected $jwt;

    public function __construct(
        UserRepository $userRepository,
        UserProviderRepository $userProviderRepository,
        JWTAuth $JWTAuth
    )
    {
        $this->userRepository         = $userRepository;
        $this->userProviderRepository = $userProviderRepository;
        $this->jwt                    = $JWTAuth;
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
     * Obtain the user information from GitHub.
     *
     * @return JsonResponse
     */
    public function handleProviderCallback($provider)
    {
        $socialUser = Socialite::driver($provider)->stateless()->user();
        try{
            $provider = $this->userProviderRepository->findBy('provider_id', $socialUser->id);
            $user = $this->userRepository->find($provider->user_id);
        }
        catch(ModelNotFoundException $exception){
            $provider = null;
            $user = null;
        }
        if(!$user){
            list($name, $lastname) = explode(' ', $socialUser->name);
            $user = $this->userRepository->save([
                'email'      => $socialUser->email,
                'first_name' => $name,
                'last_name'  => $lastname,
                'gender'     => $socialUser->user['gender']
            ]);
            $provider = $this->userProviderRepository->save([
                'user_id' => $user->id,
                'provider' => $provider,
                'provider_id' => $socialUser->id
            ]);
        }
        if($user) {
            $token = $this->jwt->fromUser($user);
            return response()->json(new JsonResponse(['token' => $token]));
        }
    }

    public function loginUser(Request $request, Manager $manager)
    {
        $this->validate($request, [
            'email'      => 'required|email',
            'password'   => 'required|min:6|max:255'
        ]);
        try{
            if(!$token = $this->jwt->attempt($request->only('email', 'password')))
            {
                return response()->json(new JsonResponse([
                    'success' => false, 'token' => null
                ], 'Bad credentials', 403), 403);
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

        return response()->json(new JsonResponse([
            'success' => true,
            'token'   => $token
        ]));
    }

    public function registerUser(Request $request)
    {
        $this->validate($request, [
            'email'      => 'required|email',
            'first_name' => 'required|min:2|max:50',
            'last_name'  => 'required|min:2|max:50',
            'gender'     => 'required',
            'password'   => 'required|min:6|max:255'
        ]);

        $user = $this->userRepository->save([
            'email'      => $request->email,
            'first_name' => $request->first_name,
            'last_name'  => $request->last_name,
            'gender'     => $request->gender,
            'birthday'   => $request->birthday,
            'password'   => $request->password
        ]);
        if($user){
            $token = $this->jwt->fromUser($user);
            return response()->json(new JsonResponse(['success' => true, 'token' => $token]));
        }
        else{
            return response()->json(new JsonResponse(['success' => true, 'token' => null]));
        }
    }

}
