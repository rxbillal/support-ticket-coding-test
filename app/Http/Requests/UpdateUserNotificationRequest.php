<?php

namespace App\Http\Requests;

/**
 * Class UpdateUserNotificationRequest
 */
class UpdateUserNotificationRequest extends \App\Http\Requests\APIRequest
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

    public function rules()
    {
        $rules = [
            'is_subscribed' => 'required',
        ];

        return $rules;
    }
}
