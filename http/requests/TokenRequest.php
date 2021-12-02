<?php

namespace Sv\JWTAuth\Http\Requests;

use Sv\JWTAuth\Http\Requests\Request;

class TokenRequest extends Request
{
    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        return [
            'token' => 'required'
        ];
    }
}
