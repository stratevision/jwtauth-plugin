<?php

namespace Sv\JWTAuth\Http\Requests;

use Sv\JWTAuth\Http\Requests\Request;

class LineLoginRequest extends Request
{
    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        return [
            'idToken'     => 'required',
            'accessToken' => 'required'
        ];
    }

    /**
     * Get tokens from request
     *
     * @return array
     */
    public function getTokens()
    {
        return [
            'idToken'     => $this->get('idToken'),
            'accessToken' => $this->get('accessToken')
        ];
    }
}
