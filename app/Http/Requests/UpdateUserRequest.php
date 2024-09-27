<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdateUserRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        $requestId = $this->route('user') == null ? $this->route('admin')->id : $this->route('user')->id;

        return [
            'name'  => 'required',
            'email' => 'required|email|unique:users,email,'.$requestId.'|regex:/^[\w\-\.\+]+\@[a-zA-Z0-9\.\-]+\.[a-zA-z0-9]{2,4}$/',
            'phone' => 'nullable|unique:users,phone,'.$requestId,
        ];
    }
}
