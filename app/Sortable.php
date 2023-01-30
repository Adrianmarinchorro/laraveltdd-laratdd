<?php

namespace App;

use Illuminate\Support\Arr;
use Illuminate\Support\Str;

class Sortable
{

    protected $currentUrl;
    protected $query = [];

    public function __construct($currentUrl)
    {
        $this->currentUrl = $currentUrl;
    }

    public function url($column)
    {
        // si estamos ordenando por la columna se ordena desc
        if ($this->isSortingBy($column)) {
            return $this->buildSortableUrl($column . '-desc');
        }

        // si no se ordena asc
        return $this->buildSortableUrl($column);

    }

    protected function buildSortableUrl($order)
    {
        return $this->currentUrl . '?' . Arr::query(array_merge($this->query, ['order' => $order]));
    }

    protected function isSortingBy($column)
    {
        return Arr::get($this->query, 'order') == $column;
    }

    public function classes($column)
    {
        if ($this->isSortingBy($column)) {
            return 'link-sortable link-sorted-up';
        }

        if ($this->isSortingBy($column . '-desc')) {
            return 'link-sortable link-sorted-down';
        }

        return 'link-sortable';
    }

    public function appends(array $query)
    {
        $this->query = $query;
    }

    public static function info(string $order)
    {
        if(Str::endsWith($order, '-desc')) {
            return [Str::substr($order, 0, -5), 'desc'];
        }

        return [$order, 'asc'];
    }

}