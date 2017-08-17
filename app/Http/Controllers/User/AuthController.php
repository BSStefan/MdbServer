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
        return Socialite::driver($provider)->redirect();
    }

    /**
     * Obtain the user information from GitHub.
     *
     * @return Response
     */
    public function handleProviderCallback($provider)
    {
        $socialUser = Socialite::driver($provider)->user();
        list($name, $lastname) = explode(' ', $user->name);
        try{
            $user = $this->userRepository->findBy('email', $socialUser->email);
        }
        catch(ModelNotFoundException $exception){
            $user = null;
        }
        if(!$user){
            $this->userRepository->save([
                'email' => $user->email,

            ]);
        }
        var_dump($user->email);
        var_dump($user->id);
        var_dump($user->user['gender']);
        list($name, $lastname) = explode(' ', $user->name);
        var_dump($name);
        var_dump($lastname);
    }

}
