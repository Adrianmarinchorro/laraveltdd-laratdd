<?php

namespace App\Http\Controllers;

use App\Skill;
use Illuminate\Http\Request;

class SkillController extends Controller
{
    public function index()
    {
        return view('skills.index', [
           'title' => 'Listado de habilidades',
           'skills' => Skill::orderBy('name')->get(),
        ]);
    }
}
