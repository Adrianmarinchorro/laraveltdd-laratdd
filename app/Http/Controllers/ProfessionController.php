<?php

namespace App\Http\Controllers;

use App\Profession;
use Illuminate\Http\Request;

class ProfessionController extends Controller
{

    public function index()
    {
        $professions = Profession::query()
            ->withCount('profiles')
            ->orderBy('title')
            ->get();

        return view('professions.index', [
            'title' => 'Listado de profesiones',
            'professions' => $professions
        ]);
    }

    public function destroy(Profession $profession)
    {
        abort_if($profession->profiles()->exists(), 400, 'Cannot deletes a profession linked to a profile');

        $profession->delete();

        return redirect()->route('profession.index');
    }

}
