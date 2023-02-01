<?php

namespace App\Filters;

use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;

abstract class QueryFilter
{
    protected $valid = [];

    abstract public function rules(): array;

    public function applyTo($query, array $filters)
    {

        $rules = $this->rules();

        $validator = Validator::make(array_intersect_key($filters, $rules), $rules);

        $this->valid = $validator->valid();

        foreach ($this->valid as $name => $value){
            $this->applyFilters($query, $name, $value);
        }

        return $query;
    }

    public function applyFilters($query, $name, $value): void
    {
        $method = Str::studly($name);

        if (method_exists($this, $method)) {
            $this->$method($query, $value); //llama al metodo de la clase
        } else {
            $query->where($name, $value); // modifica la consulta
        }
    }

    public function valid()
    {
        return $this->valid;
    }
}