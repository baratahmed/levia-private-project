<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;

class HardResetLevia extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'levia:hardReset';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Hard Reset Everything, Rerun all migrations, Feed essential data to tables';

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

        $bar = $this->output->createProgressBar(3);
        $bar->start();
        $this->line('');


        $this->call('migrate:fresh');
        $bar->advance();$this->line('');

        $this->call('levia:config');
        $bar->advance();$this->line('');

        $this->call('db:seed');
        $bar->advance();$this->line('');

        $bar->finish();
    }
}
