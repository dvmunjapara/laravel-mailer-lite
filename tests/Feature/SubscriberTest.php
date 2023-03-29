<?php

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Testing\Fluent\AssertableJson;

uses(RefreshDatabase::class);


beforeEach(function () {

    User::factory()->create();

    $subscriber =  \Pest\Laravel\post('/api/subscribers', [
        'name' => 'Subscriber 1',
        'email' => 'subscriber+1@company.com',
        'country' => 'USA'
    ], ['email' => 'john.doe@company.com'])->assertJson(function (AssertableJson $json) {
        $json->has('subscriber.id')->etc();
    });

    $this->subscriber = $subscriber->json()['subscriber'];

    $this->client = new \MailerLite\MailerLite(['api_key' => env('MAILER_LITE_TOKEN')]);

});


test('mailer lite token is set', function () {

    \PHPUnit\Framework\assertNotEmpty(env('MAILER_LITE_TOKEN'));
});


it('can create subscribers', function () {

    $sub = \Pest\Laravel\post('/api/subscribers', [
        'name' => 'Subscriber 1',
        'email' => 'subscriber+1@company.com',
        'country' => 'USA'
    ], ['email' => 'john.doe@company.com'])->assertJson(function (AssertableJson $json) {
        $json->has('subscriber.id')->etc();
    });

    $subscriber = $sub->json()['subscriber'];

    expect($subscriber['fields']['name'])->toBe('Subscriber 1')
        ->and($subscriber['fields']['country'])->toBe('USA')
        ->and($subscriber['email'])->toBe('subscriber+1@company.com');

    $res = $this->client->subscribers->find($subscriber['id']);


    \PHPUnit\Framework\assertEquals($res['body']['data']['email'], 'subscriber+1@company.com');
});

it('can edit subscribers', function () {

    $sub = \Pest\Laravel\post('/api/subscribers', [
        'id' => $this->subscriber['id'],
        'name' => 'Subscriber 1 EDIT',
        'country' => 'India'
    ], ['email' => 'john.doe@company.com'])->assertJson(function (AssertableJson $json) {
        $json->has('subscriber.id')->etc();
    });

    $subscriber = $sub->json()['subscriber'];

    expect($subscriber['fields']['name'])->toBe('Subscriber 1 EDIT')
        ->and($subscriber['fields']['country'])->toBe('India');

    $res = $this->client->subscribers->find($subscriber['id']);

    \PHPUnit\Framework\assertEquals($res['body']['data']['fields']['name'], 'Subscriber 1 EDIT');
    \PHPUnit\Framework\assertEquals($res['body']['data']['fields']['country'], 'India');
});

it('can list subscribers', function () {

    \Pest\Laravel\get('/api/subscribers?per_page=10', [
        'email' => 'john.doe@company.com'
    ])->assertJson(function (AssertableJson $json) {
        $json->count('data', 1)->etc();
    })->assertJson(function (AssertableJson $json) {
        $json->has('meta.current_page')
            ->has('meta.total')
            ->where('meta.current_page', 1)
            ->where('meta.per_page', 10)
            ->etc();
    });
});

afterEach(function () {

    $this->client->subscribers->delete($this->subscriber['id']);
});
