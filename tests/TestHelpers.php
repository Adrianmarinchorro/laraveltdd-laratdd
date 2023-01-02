<?php

namespace Tests;

trait TestHelpers
{

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

    protected function getValidData(array $custom = []): array
    {
        return array_merge($this->defaultData(), $custom);
    }

    protected function defaultData()
    {
        return $this->defaultData;
    }
}