<?php

namespace App;

class UserFilter extends QueryFilter
{

    public function rules(): array
    {
       return [
           'search' => 'filled',
           'state' => 'in:active,inactive',
           'role' => 'in:user,admin', // se aplica por defecto
       ];
    }

    public function filterBysearch($query, $search)
    {
        return $query->whereRaw('CONCAT(first_name, " ", last_name) like ?', "%{$search}%")
            ->orWhere('email', 'like', "%{$search}%")
            ->orWhereHas('team', function ($query) use ($search) {
                $query->where('name', 'like', "%{$search}%");
            });
    }

    public function filterByState($query, $state)
    {
        return $query->where('active', $state == 'active');
    }
}