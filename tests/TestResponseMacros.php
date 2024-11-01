<?php

namespace Tests;

use Illuminate\Testing\TestResponse;

trait TestResponseMacros
{
    /**
     * SetUp TestResponseMacros
     */
    public function setUpTestResponseMacros(): void
    {
        TestResponse::macro('assertPaginatedStructure', function (array $structure, int $count = null) {
            if (!is_null($count)) {
                $this->assertJsonCount($count, 'data');
            }

            $this->assertJsonStructure(['data' => ['*' => $structure]]);
        });
    }
}
