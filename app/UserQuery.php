<?php

namespace App;


class UserQuery extends QueryBuilder
{
    public function findByEmail($email)
    {
        return $this->whereEmail($email)->first();
    }
}