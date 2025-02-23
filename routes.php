<?php

Route::group(
    [
        'prefix'     => 'api/auth',
        'namespace'  => 'Sv\JWTAuth\Http\Controllers',
        'middleware' => ['api'],
    ],
    function () {
        Route::post(
            'line-login',
            'LineLoginController'
        )->name('api.auth.line-login');

        Route::post(
            'login',
            'LoginController'
        )->name('api.auth.login');

        Route::post(
            'register',
            'RegisterController'
        )->name('api.auth.register');

        Route::post(
            'account-activation',
            'ActivateController'
        )->name('api.auth.account-activation');

        Route::post(
            'forgot-password',
            'ForgotPasswordController'
        )->name('api.auth.forgot-password');

        Route::post(
            'reset-password',
            'ResetPasswordController'
        )->name('api.auth.reset-password');

        Route::post(
            'refresh-token',
            'RefreshTokenController'
        )->name('api.auth.refresh-token');

        Route::middleware(['jwt.auth'])->group(
            function () {
                Route::get(
                    'me',
                    'GetUserController'
                )->name('api.auth.me');

                Route::post(
                    'me',
                    'UpdateUserController'
                )->name('api.auth.me');
            }
        );
    }
);
