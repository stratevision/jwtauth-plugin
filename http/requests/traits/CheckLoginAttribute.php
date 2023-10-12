<?php

namespace Sv\JWTAuth\Http\Requests\Traits;

use RainLab\User\Models\Settings as WinterUserSettings;

trait CheckLoginAttribute
{
    /**
     * Check if Winter user is using the username as login field
     *
     * @return boolean
     */
    protected function isUsernameLoginAttribute()
    {
        return WinterUserSettings::get(
            'login_attribute',
            WinterUserSettings::LOGIN_EMAIL
        ) == WinterUserSettings::LOGIN_USERNAME;
    }
}
