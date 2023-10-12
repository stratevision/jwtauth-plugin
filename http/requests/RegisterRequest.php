<?php

namespace Sv\JWTAuth\Http\Requests;

use Sv\JWTAuth\Http\Requests\Request;

class RegisterRequest extends Request
{
    /**
     * {@inheritDoc}
     */
    public function data()
    {
        $data = $this->all();

        // Password confirmation is optional
        if (!array_key_exists('password_confirmation', $data)) {
            $data['password_confirmation'] = $data['password'];
        }

        return $data;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        $rules = [
            'email'    => 'required|between:3,64|email|unique:users',
            'password' => 'required|between:4,64|confirmed',
        ];

        if ($this->has("username")) {
            $rules['username'] = 'required|between:3,64|unique:users';
        }

        return $rules;
    }
}
