<?php
class CApp_Auth_Request_LoginRequest extends CHTTP_FormRequest {
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize() {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules() {
        return [
            CApp_Auth::username() => 'required|string',
            'password' => 'required|string',
        ];
    }
}
