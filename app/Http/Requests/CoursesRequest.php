<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class CoursesRequest extends FormRequest
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
        return [
            'course_id' => 'required',
            'professor_ids' => 'required',
            'academic_period_id' => 'required',
            'size' => 'required|numeric'
        ];
    }


    /**
     * Custom messages for validation rules
     *
     * @return array
     */
    public function messages()
    {
        return [
            'course_id' => 'A course must be selected',
            'professor_ids' => 'At least one examiner should be selected',
            'academic_period_id' => 'An academic period should be selected',
            'size.required' => 'Class size is required'
        ];
    }
}
