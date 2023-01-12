<?php

namespace App\Http\Requests;

use App\Role;
use App\User;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;

class CreateUserRequest extends FormRequest
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
            'first_name' => 'required',
            'last_name' => 'required',
            'email' => 'required|email|unique:users,email',
            'password' => 'required|min:7',
            'role' => ['nullable', Rule::in(Role::getList())],
            'bio' => 'required',
            'twitter' => [
                'nullable',
                'url'
            ],
            'profession_id' => [
                'nullable',
                Rule::exists('professions', 'id')->where('selectable', true),
                Rule::exists('professions', 'id')->whereNull('deleted_at')
            ],
            'skills' => [
                'array',
                Rule::exists('skills', 'id')
                ],


        ];
    }

    public function messages()
    {
        return [
            'first_name.required' => 'El nombre es obligatorio',
            'last_name.required' => 'Los apellidos son obligatorios',
            'email.required' => 'El correo electrónico es obligatorio',
            'email.unique' => 'El correo electrónico debe ser único',
            'email.email' => 'El correo electrónico debe ser válido',
            'password.required' => 'La contraseña es obligatoria',
            'password.min' => 'La contraseña debe tener mas de seis caracteres',
            'bio.required' => 'La biografia es obligatoria',
            'twitter.url' => 'El twitter debe ser una url',
            'profession_id.exists' => 'La profesión debe ser válida',

        ];
    }

    public function createUser()
    {
        DB::transaction(function() {

            $user = User::create([
                'first_name' => $this->first_name,
                'last_name' => $this->last_name,
                'email' => $this->email,
                'password' => bcrypt($this->password),
                'role' => $this->role ?? 'user',
            ]);

            $user->profile()->create([
                'bio' => $this->bio,
                'profession_id' => $this->profession_id,
                'twitter' => $this->twitter, // $data['twitter'] ?? null = ya no es necesario por la regla present
            ]);

            $user->skills()->attach($this->skills);

        });
    }
}
