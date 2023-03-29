<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use MailerLite\MailerLite;

class TestCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'run:test';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Command description';

    /**
     * Execute the console command.
     */
    public function handle(): void
    {
        $client = new MailerLite(['api_token' => 'asas']);


        dd($client->subscribers);


    }
}
