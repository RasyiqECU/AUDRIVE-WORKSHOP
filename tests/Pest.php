<?php

/*
|--------------------------------------------------------------------------
| Test Case (Pest) — bootstrap test suite AUDrive
|--------------------------------------------------------------------------
*/

pest()->extend(Tests\TestCase::class)->in('Unit');

pest()->extend(Tests\TestCase::class)
    ->use(Illuminate\Foundation\Testing\RefreshDatabase::class)
    ->in('Feature');
