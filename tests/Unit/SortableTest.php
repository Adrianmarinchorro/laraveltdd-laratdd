<?php

namespace Tests\Unit;

use App\Sortable;
use Tests\TestCase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Foundation\Testing\RefreshDatabase;

class SortableTest extends TestCase
{
    protected $sortable;

    protected function setUp(): void
    {
        parent::setUp(); // ejecuta lo que hay en el padre

        $this->sortable = new Sortable('http://laratdd.local/demo');
    }

    /** @test */
    function return_a_css_class_to_indicate_the_column_is_sortable()
    {
        // decimos que la clase de css es igual que lo que obtiene del objeto sortable cuando llama al metodo classes al recibir el parametro
        $this->assertSame('link-sortable', $this->sortable->classes('first_name'));
    }


    /** @test */
    function return_a_css_class_to_indicate_the_column_is_sorted_in_ascendent_order()
    {
        $this->sortable->setCurrentOrder('first_name'); //establece el orden por esa columna

        $this->assertSame('link-sortable link-sorted-up', $this->sortable->classes('first_name'));
    }

    /** @test */
    function return_a_css_class_to_indicate_the_column_is_sorted_in_descendent_order()
    {
        $this->sortable->setCurrentOrder('first_name', 'desc'); //establece el orden de esa columna en este momento

        $this->assertSame('link-sortable link-sorted-down', $this->sortable->classes('first_name'));
    }

    /** @test */
    function build_a_url_with_sortable_data()
    {
        //generamos el objeto con el constructor recibiendo una url
        //$sortable = new Sortable('http://laratdd.local/demo');

        $this->assertSame('http://laratdd.local/demo?order=first_name&direction=asc', $this->sortable->url('first_name'));
    }

    /** @test */
    function build_a_url_with_desc_order_if_the_current_column_matches_the_given_one_and_the_current_direction_is_asc()
    {
        $this->sortable->setCurrentOrder('first_name', 'asc');

        $this->assertSame('http://laratdd.local/demo?order=first_name&direction=desc', $this->sortable->url('first_name'));
    }

}
