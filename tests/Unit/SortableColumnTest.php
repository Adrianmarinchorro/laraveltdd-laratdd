<?php

namespace Tests\Unit;

use Tests\TestCase;
use App\Rules\SortableColumn;

class SortableColumnTest extends TestCase
{

    /** @test */
    function validates_sortable_values()
    {
        $rule = new SortableColumn(['first_name', 'id', 'email', 'date']);

        $this->assertTrue($rule->passes('order', 'first_name'));
        $this->assertTrue($rule->passes('order', 'id'));
        $this->assertTrue($rule->passes('order', 'id-desc'));
        $this->assertTrue($rule->passes('order', 'email'));
        $this->assertTrue($rule->passes('order', 'date'));
        $this->assertTrue($rule->passes('order', 'first_name-desc'));
        $this->assertTrue($rule->passes('order', 'email-desc'));
        $this->assertTrue($rule->passes('order', 'date-desc'));

        $this->assertFalse($rule->passes('order', []));
        $this->assertFalse($rule->passes('order', false));
        $this->assertFalse($rule->passes('order', 'first_name-descendent'));
        $this->assertFalse($rule->passes('order', 'asc-name'));
        $this->assertFalse($rule->passes('order', 'name'));
        $this->assertFalse($rule->passes('order', 'email-descx'));
        $this->assertFalse($rule->passes('order', 'desc-first_name'));
    }
}
