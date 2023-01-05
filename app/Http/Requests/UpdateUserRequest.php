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
        return [
            'name' => 'required',
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
            'name.required' => 'El nombre es obligatorio',
            'email.required' => 'El correo electrónico es obligatorio',
            'email.email' => 'El correo electrónico debe ser valido',
            'email.unique' => 'El correo electrónico debe ser único',
        ];
    }

    public function updateUser(User $user)
    {
        $data = $this->validated();

        if ($data['password'] != null) {
            $data['password'] = bcrypt($data['password']);
        } else {
            unset($data['password']);
        }


        $user->fill($data);
        $user->role = $data['role'];
        $user->save();

        $user->profile->update($data);

        $user->skills()->sync($data['skills'] ?? []);

    }

}
