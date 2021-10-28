<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class MyCourseRequest extends FormRequest
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
    public static function rules()
    {
        return [
            'course_id' => 'required|integer',
            'user_id' => 'required|integer',
        ];
    }
}
