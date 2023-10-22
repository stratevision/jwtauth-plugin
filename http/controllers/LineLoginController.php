<?php

namespace Sv\JWTAuth\Http\Controllers;

use Event;
use Illuminate\Http\Response;
use Illuminate\Routing\Controller;
use Sv\JWTAuth\Classes\JWTAuth;
use PHPOpenSourceSaver\JWTAuth\Exceptions\JWTException;
use Sv\JWTAuth\Http\Requests\LineLoginRequest;

class LineLoginController extends Controller
{
    /**
     * Login the user
     *
     * @param JWTAuth $auth
     * @param LoginRequest $request
     *
     * @return Illuminate\Http\Response
     */
    public function __invoke(
        JWTAuth          $auth,
        LineLoginRequest $request
    )
    {
        $tokens = $request->getTokens();

        // Event::fire('RainLab.User.beforeAuthenticate', [$this, $credentials]);

        $providers = \Flynsarmy\SocialLogin\Models\Settings::get('providers');
        $client_id = $providers['Line']['client_id'];

        // ============
        // Line verify
        // ============

        // https://developers.line.biz/en/docs/line-login/secure-login-process/#using-openid-to-register-new-users
        $url = 'https://api.line.me/oauth2/v2.1/verify';
        $client = new \GuzzleHttp\Client();
        $response = $client->post($url, [
            'headers'     => [
                'Content-Type' => 'application/x-www-form-urlencoded',
            ],
            'form_params' => [
                'id_token'  => $tokens['idToken'],
                'client_id' => $client_id,
                //'nonce'     => $nonce,
            ],
            'http_errors' => false,
        ]);

        $output = json_decode($response->getBody(), false);

        if (isset($output->error)) {
            return $output->error_description;
        }

        // 檢查該 username 是否存在資料庫中。
        $user = \RainLab\User\Models\User::where('username', $output->sub)->first();

        if (!$user) {
            $user = \Auth::findUserByLogin($output->email);
            if (!$user) {
                $user = \Auth::register([
                    'name'                  => $output->name,
                    'username'              => $output->sub,
                    'email'                 => $output->email,
                    'password'              => 'changeme',
                    'password_confirmation' => 'changeme',
                ], true);
            } else {
                if (preg_match('/^09\d{8}$/', $user->username) == false) {
                    $user->username = $output->sub;
                    $user->save();
                }
            }
        } else {
            // 若該會員沒有 email，並且該 email 不存在，則更新之。
            if (\Str::contains($user->email, '@dev.null') && !\RainLab\User\Models\User::where('email', '=', $output->email)->exists()) {
                $user->email = $output->email;
                $user->save();
            }
        }

        try {
            // Get Sv\JWTAuth\Models\User JWTSubject
            $user = $auth->findUserById($user->id);

            // Log a user in and return a jwt for them
            if (!$token = $auth->fromUser($user)) {
                return response()->json(
                    ['error' => 'invalid'],
                    Response::HTTP_UNAUTHORIZED
                );
            }
        } catch (JWTException $e) {
            return response()->json(
                ['error' => 'could_not_create_token'],
                Response::HTTP_UNAUTHORIZED
            );
        }

        // $user = $auth->setToken($token)->authenticate();

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
