<?php

namespace Sv\JWTAuth\Http\Controllers;

use Mail;
use Illuminate\Http\Response;
use Sv\JWTAuth\Models\User;
use Sv\JWTAuth\Models\Settings;
use Illuminate\Routing\Controller;
use Sv\JWTAuth\Http\Requests\ForgotPasswordRequest;
use Sv\JWTAuth\Http\Controllers\Traits\CanMakeUrl;
use Sv\JWTAuth\Http\Controllers\Traits\CanSendMail;

class ForgotPasswordController extends Controller
{
    use CanMakeUrl,
        CanSendMail;

    /**
     * Send the forgot password request
     *
     * @param ForgotPasswordRequest $request
     *
     * @return Illuminate\Http\Response
     */
    public function __invoke(
        ForgotPasswordRequest $request
    ) {
        $email = $request->get('email');

        $user = User::findByEmail($email);
        if (!$user || $user->isBanned() || !$user->is_activated) {
            return response()->json(
                ['error' => 'user_not_found'],
                Response::HTTP_NOT_FOUND
            );
        }

        $this->sendResetPasswordEmail($user);

        return response()->json([]);
    }

    /**
     * Sends the forgot password email to a user
     *
     * @param User $user
     * @return void
     */
    protected function sendResetPasswordEmail(User $user)
    {
        $code = implode('!', [$user->id, $user->getResetPasswordCode()]);
        $link = $this->makeResetPasswordUrl($code);

        $data = [
            'name' => $user->name,
            'link' => $link,
            'code' => $code
        ];

        $this->sendMail(
            $user->email,
            $user->name,
            'RainLab.User::mail.restore',
            $data
        );
    }

    /**
     * Create the password reset URL
     *
     * @param string $code
     * @return string
     */
    protected function makeResetPasswordUrl($code)
    {
        $url = Settings::get('reset_password_url');
        return $this->makeUrl($url, $code);
    }
}
