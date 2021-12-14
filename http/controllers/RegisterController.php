<?php

namespace Sv\JWTAuth\Http\Controllers;

use Mail;
use Event;
use Illuminate\Http\Response;
use Sv\JWTAuth\Models\User;
use Illuminate\Routing\Controller;
use Sv\JWTAuth\Classes\JWTAuth;
use Sv\JWTAuth\Models\Settings;
use Sv\JWTAuth\Http\Requests\RegisterRequest;
use Sv\JWTAuth\Http\Controllers\Traits\CanMakeUrl;
use RainLab\User\Models\Settings as WinterUserSettings;
use Sv\JWTAuth\Http\Controllers\Traits\CanSendMail;

/**
 * @group JWTAuth
 */
class RegisterController extends Controller
{
    use CanMakeUrl,
        CanSendMail;

    /**
     * Register the user
     *
     * @param JWTAuth         $auth
     * @param RegisterRequest $request
     *
     * @return Illuminate\Http\Response
     */
    public function __invoke(
        JWTAuth $auth,
        RegisterRequest $request
    ) {
        if (!$this->canRegister()) {
            return response()->json(
                ['error' => 'registration_disabled'],
                Response::HTTP_UNAUTHORIZED
            );
        }

        $data = $request->all();

        Event::fire('RainLab.User.beforeRegister', [&$data]);

        $activationMode = $this->getActivationMode();
        $user = $auth->register($data, ($activationMode == 'auto'));

        Event::fire('RainLab.User.register', [$user, $data]);

        if ($activationMode == 'email') {
            $this->sendActivationEmail($user);
        }

        return response()->json([], Response::HTTP_CREATED);
    }

    /**
     * Check if the settings allow user registration
     *
     * @return boolean
     */
    protected function canRegister()
    {
        return WinterUserSettings::get('allow_registration', true);
    }

    /**
     * Get the activation mode from configuration as string
     *
     * @return string
     */
    protected function getActivationMode()
    {
        switch (WinterUserSettings::get('activate_mode')) {
            case WinterUserSettings::ACTIVATE_USER:
                return 'email';
            case WinterUserSettings::ACTIVATE_AUTO:
                return 'auto';
        }

        return 'manual';
    }

    /**
     * Sends the activation email to a user
     *
     * @param User $user
     *
     * @return void
     */
    protected function sendActivationEmail(User $user)
    {
        $code = implode('!', [$user->id, $user->getActivationCode()]);
        $link = $this->makeActivationUrl($code);

        $data = [
            'name' => $user->name,
            'link' => $link,
            'code' => $code
        ];

        $this->sendMail(
            $user->email,
            $user->name,
            'RainLab.User::mail.activate',
            $data
        );
    }

    /**
     * Returns a link used to activate the user account.
     *
     * @param string $code
     *
     * @return string
     */
    protected function makeActivationUrl($code)
    {
        $url = Settings::get('activation_url');
        return $this->makeUrl($url, $code);
    }
}
