<?php

namespace Sv\JWTAuth\Http\Requests;

use Sv\JWTAuth\Http\Requests\Request;

class ActivationRequest extends Request
{
    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        return [
            'activation_code' => 'required'
        ];
    }
}
