<?php

namespace Sv\JWTAuth\Http\Requests\Traits;

use RainLab\User\Models\Settings as UserSettings;

trait CheckLoginAttribute
{
    /**
     * Check if user is using the username as login field
     *
     * @return boolean
     */
    protected function isUsernameLoginAttribute()
    {
        return UserSettings::get(
            'login_attribute',
            UserSettings::LOGIN_EMAIL
        ) == UserSettings::LOGIN_USERNAME;
    }
}
