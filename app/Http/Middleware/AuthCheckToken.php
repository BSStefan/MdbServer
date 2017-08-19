<?php

namespace App\Http\Middleware;

use App\Http\Response\JsonResponse;
use Closure;
use Tymon\JWTAuth\Exceptions\JWTException;
use Tymon\JWTAuth\JWTAuth;

class AuthCheckToken
{
    /**
     * @var JWTAuth
     */
    protected $auth;

    /**
     * Create a new AuthCheckToken instance.
     *
     * @param  \Tymon\JWTAuth\JWTAuth $auth
     */
    public function __construct(JWTAuth $auth)
    {
        $this->auth = $auth;
    }

    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle($request, Closure $next)
    {
        try{
            if ($this->auth->parser()->setRequest($request)->hasToken()) {
                $this->auth->parseToken()->authenticate();
            }
            else{
                throw new JWTException();
            }

            return $next($request);
        }
        catch(\Exception $e){
            return response()->json(new JsonResponse(
                ['success' => false,'excaption' => $e->getMessage()],
                'Token missing',
                401
            ));
        }
    }
}
