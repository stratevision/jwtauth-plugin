<?php

namespace Sv\JWTAuth\Http\Controllers;

use Illuminate\Http\Response;
use Sv\JWTAuth\Classes\JWTAuth;
use Illuminate\Routing\Controller;

/**
 * @group JWTAuth
 * @authenticated
 */
class GetUserController extends Controller
{
    /**
     * Get User Profile
     *
     * @return Illuminate\Http\Response
     */
    public function __invoke(JWTAuth $auth)
    {
        if (!$user = $auth->user()) {
            return response()->json(
                ['error' => 'user_not_found'],
                Response::HTTP_NOT_FOUND
            );
        }

        return response()->json(compact('user'));
    }
}
