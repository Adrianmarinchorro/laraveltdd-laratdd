<?php

namespace Tests;

use Illuminate\Testing\TestResponse;
use Illuminate\Foundation\Testing\TestCase as BaseTestCase;

abstract class TestCase extends BaseTestCase
{
    use CreatesApplication, TestHelpers, DetectRepeatedQueries;

    protected $defaultData = [];

    protected function setUp(): void
    {
        parent::setUp();

        $this->addTestResponseMacros();

        $this->withoutExceptionHandling();

        //habilita el log de las consultas
        $this->enableQueryLog();
    }

    protected function tearDown(): void
    {
        $this->flushQueryLog();
        parent::tearDown();
    }

    /**
     * @return void
     */
    public function addTestResponseMacros(): void
    {
        TestResponse::macro('viewData', function ($key) {
            $this->ensureResponseHasView();

            $this->assertViewHas($key);

            return $this->original->$key;
        });

        TestResponse::macro('assertViewCollection', function ($var) {
            return new TestCollectionData($this->viewData($var));
        });
    }

}
