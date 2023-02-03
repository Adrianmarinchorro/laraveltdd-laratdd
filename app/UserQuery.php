<?php

namespace App;


class UserQuery extends QueryBuilder
{
    public function findByEmail($email)
    {
        return $this->whereEmail($email)->first();
    }


    public function withLastLogin()
    {
        // mediante la clase login seleccionamos el id de la fila donde la id del usuario es igual que la del objeto user, y con el metodo latest
        // obtenemos el ultimo registro es como hacer un orderByDesc('created_at'), hacemos un limit de 1 porque solo queremos uno y usamos get.
        $subselect = Login::select('logins.created_at')->whereColumn('logins.user_id', 'users.id')->latest()->limit(1);

        return $this->addSelect([
            'last_login_at' => $subselect //estamos asignando el valor a la propiedad
        ]);
    }

}