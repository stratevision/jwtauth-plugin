<?php

namespace Sv\JWTAuth\Http\Controllers;

use Event;
use Illuminate\Http\Response;
use Illuminate\Routing\Controller;
use Sv\JWTAuth\Classes\JWTAuth;
use Tymon\JWTAuth\Exceptions\JWTException;
use Sv\JWTAuth\Http\Requests\LoginRequest;

class LoginController extends Controller
{
    /**
     * Login the user
     *
     * @param JWTAuth      $auth
     * @param LoginRequest $request
     *
     * @return Illuminate\Http\Response
     */
    public function __invoke(
        JWTAuth $auth,
        LoginRequest $request
    ) {
        $credentials = $request->getCredentials();

        Event::fire('RainLab.User.beforeAuthenticate', [$this, $credentials]);

        try {
            if (!$token = $auth->attempt($credentials)) {
                return response()->json(
                    ['error' => 'invalid_credentials'],
                    Response::HTTP_UNAUTHORIZED
                );
            }
        } catch (JWTException $e) {
            return response()->json(
                ['error' => 'could_not_create_token'],
                Response::HTTP_UNAUTHORIZED
            );
        }

        $user = $auth->setToken($token)->authenticate();

        if ($user->isBanned()) {
            $auth->invalidate();
            return response()->json(
                ['error' => 'user_is_banned'],
                Response::HTTP_UNAUTHORIZED
            );
        }

        if (!$user->is_activated) {
            $auth->invalidate();
            return response()->json(
                ['error' => 'user_inactive'],
                Response::HTTP_UNAUTHORIZED
            );
        }

        Event::fire('RainLab.User.login', $user);
        return response()->json(compact('token', 'user'));
    }
}
