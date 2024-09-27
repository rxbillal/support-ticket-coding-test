<?php

namespace App\Http\Requests;

use App\Models\Ticket;
use Illuminate\Foundation\Http\FormRequest;

class CreateTicketRequest extends FormRequest
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
        $rules = Ticket::$rules;
        $rules['user_name'] = getLoggedInUser() ? '' : 'required|string';
        $rules['email'] = getLoggedInUser() ? '' : 'required|email|unique:users,email|regex:/^[\w\-\.\+]+\@[a-zA-Z0-9\.\-]+\.[a-zA-z0-9]{2,4}$/';
        $rules['password'] = getLoggedInUser() ? '' : 'required|min:6|same:confirm_password';
        $rules['confirm_password'] = getLoggedInUser() ? '' : 'required|min:6';

        return $rules;
    }
}
