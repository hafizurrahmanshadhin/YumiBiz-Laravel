<?php

namespace App\Http\Requests;

use App\Helpers\Helper;
use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Exceptions\HttpResponseException;

class EditProfileRequest extends FormRequest {
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
            'name'                => 'sometimes|string|max:100',
            'email'               => 'sometimes|string|email|max:100|unique:users,email,' . auth()->id(),
            'user_name'           => 'sometimes|string|max:100',
            'age'                 => 'sometimes|integer|min:18|max:120',
            'gender'              => 'sometimes|string|in:male,female',
            'phone'               => 'sometimes|string|max:20',
            'willing_to_invest'   => 'sometimes|string',
            'bio'                 => 'sometimes|string|max:201',
            'country'             => 'sometimes|string|max:100',
            'city'                => 'sometimes|string|max:100',
            'state'               => 'sometimes|string|max:100',
            'province'            => 'sometimes|string|max:100',
            'degree'              => 'sometimes|string|max:100',
            'institute'           => 'sometimes|string|max:150',
            'academic_year_start' => 'sometimes',
            'academic_year_end'   => 'sometimes',
            'images.*'            => 'sometimes|image|mimes:jpeg,png,jpg,gif,svg|max:12288',
            'designation'         => 'sometimes|string|max:100',
            'company_name'        => 'sometimes|string|max:150',
            'experience_from'     => 'sometimes',
            'experience_to'       => 'sometimes',
            'meta_id'             => 'sometimes|exists:metas,id',
        ];
    }

    /**
     * Handle a failed validation attempt.
     *
     * @param Validator $validator
     * @throws HttpResponseException
     * @return void
     */
    protected function failedValidation(Validator $validator): void {
        $response = Helper::jsonResponse(false, $validator->errors(), 422);
        throw new HttpResponseException($response);
    }
}
