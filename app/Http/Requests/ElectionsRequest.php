<?php

namespace App\Http\Requests;

use App\Exceptions\ApiFailedException;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Foundation\Http\FormRequest;

class ElectionsRequest extends FormRequest
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
            'libelle' => 'required | string',
            'tete_liste' => 'required | string',
            'contact' => 'required | string',
            'description' => ' | string',
            'id_grade' => 'required | number',
            'id_categorie' => 'required | number',
            'user1d' => 'required | number',
            'type_election' => 'required | number',
        ];
    }

    /**
     * Handle a failed validation attempt for API.
     */
    protected function failedValidation(Validator $validator)
    {
        if (request()->is('api/*')) {
            throw new ApiFailedException($validator->errors());
        }
    }
}
