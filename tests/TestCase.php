<?php

declare(strict_types=1);

namespace Tests;

use Illuminate\Foundation\Testing\LazilyRefreshDatabase;
use Illuminate\Foundation\Testing\TestCase as BaseTestCase;
use Tests\Concerns\CreatesSchoolScenarios;

abstract class TestCase extends BaseTestCase
{
    use CreatesSchoolScenarios;
    use LazilyRefreshDatabase;
}
