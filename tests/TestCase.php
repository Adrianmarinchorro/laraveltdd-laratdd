<?php

namespace Tests;

use Illuminate\Foundation\Testing\TestCase as BaseTestCase;

abstract class TestCase extends BaseTestCase
{
    use CreatesApplication;

    protected function assertDatabaseEmpty($table, $connection = null)
    {
        $total = $this->getConnection($connection)->table($table)->count();
        $this->assertSame(0, $total, sprintf(
            "Failed asserting the table[%s] is empty. %s %s found.", $table, $total, str_plural('row', $total)
        ));
    }

    protected function assertDatabaseCount($table, $count, $connection = null)
    {
        $total = $this->getConnection($connection)->table($table)->count();
        $this->assertSame($count, $total, sprintf(
            "Failed asserting the table[%s] has %s %s. %s %s found.", $table, $count, str_plural('row', $count), $total, str_plural('row', $total)
        ));
    }

}
