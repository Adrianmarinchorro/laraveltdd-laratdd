<?php

namespace App\Http\ViewComposers;

use App\Profession;
use App\Skill;
use Illuminate\Contracts\View\View;

class UserFieldsComposer
{

    public function compose(View $view)
    {
        $professions = Profession::orderBy('title','ASC')->get();

        $skills = Skill::orderBy('name', 'ASC')->get();

        $roles = trans('users.roles');

        return $view->with(compact('professions', 'skills', 'roles'));

    }

}