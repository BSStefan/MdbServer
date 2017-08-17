<?php

namespace App\Http\Controllers\User;

use App\Repositories\UserProviderRepository;
use App\Repositories\UserRepository;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Http\Response;
use Laravel\Socialite\Facades\Socialite;

class AuthController extends Controller
{
    protected $userRepository;

    protected $userProviderRepository;

    public function __construct(
        UserRepository $userRepository,
        UserProviderRepository $userProviderRepository
    )
    {
        $this->userRepository = $userRepository;
        $this->userProviderRepository = $userProviderRepository;
    }

    /**
     * Redirect the user to the GitHub authentication page.
     *
     * @return Response
     */
    public function redirectToProvider($provider)
    {
        var_dump(Socialite::driver($provider)->stateless()->redirect()->getTargetUrl());exit;
        return Socialite::driver($provider)->stateless()->getAuthUrl();
    }

    /**
     * Obtain the user information from GitHub.
     *
     * @return Response
     */
    public function handleProviderCallback($provider)
    {
        $socialUser = Socialite::driver($provider)->stateless()->user();
        try{
            $user = $this->userRepository->findBy('email', $socialUser->email);
        }
        catch(ModelNotFoundException $exception){
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
        }
        if($user) {
            //kreiraj mu token
        }
    }

}
