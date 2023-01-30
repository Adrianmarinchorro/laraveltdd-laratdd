<?php

namespace App;

use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class UserFilter extends QueryFilter
{

    protected $aliasses = [
        'date' => 'created_at'
    ];

    public function getColumnName($alias)
    {
        return $this->aliasses[$alias] ?? $alias;
    }

    public function rules(): array
    {
        return [
            'search' => 'filled',
            'state' => 'in:active,inactive',
            'role' => 'in:user,admin', // se aplica por defecto
            'skills' => 'array|exists:skills,id',
            'from' => 'date_format:d/m/Y',
            'to' => 'date_format:d/m/Y',
            'order' => 'in:first_name,email,date,first_name-desc,email-desc,date-desc',
        ];
    }

    public function search($query, $search)
    {
        return $query->whereRaw('CONCAT(first_name, " ", last_name) like ?', "%{$search}%")
            ->orWhere('email', 'like', "%{$search}%")
            ->orWhereHas('team', function ($query) use ($search) {
                $query->where('name', 'like', "%{$search}%");
            });
    }

    public function state($query, $state)
    {
        return $query->where('active', $state == 'active');
    }

    public function skills($query, $skills)
    {
        $subquery = DB::table('user_skill AS s')
            ->selectRaw('COUNT(s.id)')
            ->whereColumn('s.user_id', 'users.id')
            ->whereIn('skill_id', $skills);

        $query->whereQuery($subquery, count($skills));
    }

    public function from($query, $date)
    {
        // transformamos el texto plano del formulario en un objeto de la libreria carbon de tipo
        // fecha para poder manejar la fecha y usar los metodos de dicha libreria
        $date = Carbon::createFromFormat('d/m/Y', $date);

        // al ser un objeto Carbon podemos modificar la query con whereDate()
        $query->whereDate('created_at', '>=', $date);
    }

    public function to($query, $date)
    {
        $date = Carbon::createFromFormat('d/m/Y', $date);

        $query->whereDate('created_at', '<=', $date);
    }

    public function order($query, $value)
    {
        // si termina $value por -desc entonces ordena descendiente el campo substring de $value que empieza desde el principio y se le quitan las ultimas 5 letras
        if(Str::endsWith($value, '-desc')) {
            $query->orderByDesc($this->getColumnName(Str::substr($value, 0, -5)));
        } else {
            $query->orderBy($this->getColumnName($value));
        }
    }
}