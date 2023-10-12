<?php

namespace Sv\JWTAuth\Classes;

use RainLab\User\Classes\AuthManager as RainAuthManager;

/**
 * {@inheritDoc}
 */
class AuthManager extends RainAuthManager
{
    /**
     * {@inheritDoc}
     */
    protected $userModel = \Sv\JWTAuth\Models\User::class;
}
