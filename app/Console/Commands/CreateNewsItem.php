<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\News;

class CreateNewsItem extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'newsitem:new {title} {message}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Create a new newsitem';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
        $title = $this->argument('title');
        $message = $this->argument('message');

        $news_item = new News();
        $news_item->title = $title;
        $news_item->message = $message;
        $news_item->url = strtolower(trim(preg_replace('/[^a-zA-Z0-9]+/', '-', $title), '-'));
        $news_item->save();


        $this->info("News item successfully created!");

    }
}
