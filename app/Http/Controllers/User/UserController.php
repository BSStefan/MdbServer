<?php

namespace App\Http\Controllers\User;

use App\Http\Response\JsonResponse;
use App\Repositories\UserRepository;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Tymon\JWTAuth\Facades\JWTAuth;
use Illuminate\Support\Facades\Hash;

class UserController extends Controller
{
    private $userRepository;

    public function __construct(
        UserRepository $userRepository
    )
    {
        $this->userRepository = $userRepository;
    }

    public function getInfo()
    {
        $user = JWTAuth::user();

        return response()->json(new JsonResponse($user));
    }

    public function updateInfo(Request $request)
    {
        $this->validate($request, [
            'email'      => 'required|email|unique:users',
            'first_name' => 'required|min:2|max:50',
            'last_name'  => 'required|min:2|max:50',
            'gender'     => 'required',
            'birthday'   => 'nullable|date',
            'city'       => 'nullable|string'
        ]);

        $user = JWTAuth::user();

        try{
            $newUser = $this->userRepository->save([
                'email'      => $request->email,
                'first_name' => $request->first_name,
                'last_name'  => $request->last_name,
                'gender'     => $request->gender,
                'birthday'   => $request->birthday,
                'city'       => $request->city
            ], $user->id);

            return response()->json(new JsonResponse(['success' => true]));
        }
        catch(\Exception $e){
            return response()->json(new JsonResponse(['success' => false]));
        }
    }

    public function updatePassword(Request $request)
    {
        $this->validate($request, [
            'old_password' => 'min:6|max:255',
            'password'     => 'min:6|max:255'
        ]);

        $user = JWTAuth::user();

        if(Hash::check($request->old_password, $user->password)){
            try{
                $newUser = $this->userRepository->save([
                    'password' => $request->password
                ], $user->id);
                return response()->json(new JsonResponse(['success' => true]));
            }
            catch(\Exception $e){
                return response()->json(new JsonResponse(['success' => false]));
            }
        }
        return response()->json(new JsonResponse(['success' => false], 'Wrong password',400));



    }
}
