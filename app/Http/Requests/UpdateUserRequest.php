<?php

namespace App\Http\Requests;

use App\Role;
use App\User;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateUserRequest extends FormRequest
{

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
        //TODO: hacer las pruebas de estas reglas
        return [
            'first_name' => 'required',
            'last_name' => 'required',
            'email' => [
                'required',
                'email',
                Rule::unique('users')->ignore($this->user)
            ],
            'password' => 'nullable|min:7',
            'role' => [ Rule::in(Role::getList())],
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
            'email.email' => 'El correo electrónico debe ser valido',
            'email.unique' => 'El correo electrónico debe ser único',
        ];
    }

    public function updateUser(User $user)
    {
        $user->fill([
            'first_name' => $this->first_name,
            'last_name' => $this->last_name,
            'email' => $this->email,
        ]);

        if ($this->password != null) {
            $user->password = bcrypt($this->password);
        }

        $user->role = $this->role;
        $user->save();

        $user->profile->update([
            'bio' => $this->bio,
            'twitter' => $this->twitter,
            'profession_id' => $this->profession_id,
        ]);

        $user->skills()->sync($this->skills ?: []);

    }

}
