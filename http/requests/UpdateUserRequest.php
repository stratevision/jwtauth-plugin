<?php

namespace Sv\JWTAuth\Http\Requests;

use Sv\JWTAuth\Http\Requests\Request;

class UpdateUserRequest extends Request
{
    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        return [
            'name'  => 'required',
            'email' => 'required|between:6,64|email',
        ];
    }
}
