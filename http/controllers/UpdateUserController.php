<?php

namespace Sv\JWTAuth\Http\Controllers;

use Illuminate\Http\Response;
use Illuminate\Routing\Controller;
use Sv\JWTAuth\Classes\JWTAuth;
use Sv\JWTAuth\Http\Requests\UpdateUserRequest;
use PHPOpenSourceSaver\JWTAuth\Exceptions\JWTException;

class UpdateUserController extends Controller
{
    /**
     * Update user profile
     *
     * @param JWTAuth $auth
     * @param UpdateUserRequest $request
     *
     * @return Illuminate\Http\Response
     */
    public function __invoke(
        JWTAuth           $auth,
        UpdateUserRequest $request
    )
    {
        try {
            $data = $request->data();
            $user = $auth->user();
            $user->fill($data);
            $user->save();
        } catch (JWTException $e) {
            return response()->json(
                ['error' => 'Update failed!'],
                Response::HTTP_BAD_REQUEST
            );
        }

        $user->load('groups');
        $user->load('sites');
        $user->load('clusters');

        return response()->json(compact('user'));
    }
}
