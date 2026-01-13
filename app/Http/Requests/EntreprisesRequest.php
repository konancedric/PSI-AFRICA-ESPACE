<?php

namespace App\Http\Requests;

use App\Exceptions\ApiFailedException;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Foundation\Http\FormRequest;

class EntreprisesRequest extends FormRequest
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
            'denomination' => 'required | string | unique:entreprises',
            'adresse' => 'required | string',
            'contact' => 'required | string',
            'description' => ' | string',
            'emailent' => 'required | string',
            'user1d' => 'required | number',
            'id_ville' => 'required | number',
            'id_souscategorie' => 'required | string',
            'username' => 'required | string | unique:entreprises',
            'logo_ent' => 'required | string',
            'url_qr' => ' | string',
            'save_qr' => ' | string',
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
