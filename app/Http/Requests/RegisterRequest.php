<?php

namespace App\Http\Requests;

use App\Helpers\Helper;
use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Exceptions\HttpResponseException;

class RegisterRequest extends FormRequest {
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, ValidationRule|array|string>
     */
    public function rules(): array {
        return [
            'name'                  => 'nullable|string|between:2,100',
            'email'                 => 'nullable|string|email|max:100|unique:users',
            'password'              => 'nullable|string|confirmed|min:8',
            'agree_to_terms'        => 'required|boolean|in:1,true',
            'linkedin_id'           => 'nullable|string',
            'user_name'             => 'nullable|string|between:2,100',
            'age'                   => 'nullable|integer',
            'gender'                => 'nullable|string|in:male,female',
            'country'               => 'nullable|string|between:2,100',
            'city'                  => 'nullable|string|between:2,100',
            'state'                 => 'nullable|string|between:2,100',
            'province'              => 'nullable|string|between:2,100',
            'images.*'              => 'nullable|image|mimes:jpeg,png,jpg,gif|max:10240',
            'industry'              => 'nullable|string|max:255',
            'other_industry'        => 'nullable|string|max:255',
            'years_of_experience'   => 'nullable|string|max:255',
            'areas_of_expertise'    => 'nullable|array',
            'areas_of_expertise.*'  => 'nullable|string|max:255',
            'other_expertise'       => 'nullable|array',
            'other_expertise.*'     => 'nullable|string|max:255',
            'support_offer'         => 'nullable|array',
            'support_offer.*'       => 'nullable|string|max:255',
            'other_support_offer'   => 'nullable|array',
            'other_support_offer.*' => 'nullable|string|max:255',
        ];
    }

    /**
     * Handle a failed validation attempt.
     *
     * @param Validator $validator
     * @return void
     *
     * @throws HttpResponseException
     */
    protected function failedValidation(Validator $validator): void {
        $response = Helper::jsonResponse(false, $validator->errors(), 422);
        throw new HttpResponseException($response);
    }
}
