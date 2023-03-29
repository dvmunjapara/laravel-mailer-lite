<?php

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Testing\Fluent\AssertableJson;
use function Pest\Laravel\get;
use function Pest\Laravel\post;

uses(RefreshDatabase::class);

beforeEach(fn () => User::factory()->create());

test('mailer lite token is set', function () {

    \PHPUnit\Framework\assertNotEmpty(env('MAILER_LITE_TOKEN'));
});

it('can validate token', function () {

    post('/api/token', [
        'email' => 'john.doe+1@company.com',
        'token' => 'wrong_token'
    ],['accept' => 'application/json'])->assertStatus(422);
});

it('can store token', function () {

    post('/api/token', [
        'email' => 'john.doe+1@company.com',
        'token' => env('MAILER_LITE_TOKEN')
    ])->assertStatus(200);
});

it('can fetch token', function () {

    get('/api/token?email=john.doe@company.com')->assertJson(function (AssertableJson $json) {
        $json->has('user.email');
    });
});

test('token is hidden', function () {

    get('/api/token?email=john.doe@company.com')->assertJson(function (AssertableJson $json) {

        $json->missing('user.token')->etc();
    });
});
