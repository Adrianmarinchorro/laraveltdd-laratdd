<?php

namespace Tests\Unit;

use App\Sortable;
use Tests\TestCase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Foundation\Testing\RefreshDatabase;

class SortableTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    function return_a_css_class_to_indicate_the_column_is_sortable()
    {
        $sortable = new Sortable();

        // decimos que la clase de css es igual que lo que obtiene del objeto sortable cuando llama al metodo classes al recibir el parametro
        $this->assertSame('link-sortable', $sortable->classes('first_name'));
    }


    /** @test */
    function return_a_css_class_to_indicate_the_column_is_sorted_in_ascendent_order()
    {
        $sortable = new Sortable();

        $sortable->setCurrentOrder('first_name'); //establece el orden por esa columna

        $this->assertSame('link-sortable link-sorted-up', $sortable->classes('first_name'));
    }

    /** @test */
    function return_a_css_class_to_indicate_the_column_is_sorted_in_descendent_order()
    {
        $sortable = new Sortable();

        $sortable->setCurrentOrder('first_name', 'desc'); //establece el orden de esa columna en este momento

        $this->assertSame('link-sortable link-sorted-down', $sortable->classes('first_name'));
    }

}
