<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;

class DisconfigureLevia extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'levia:drop';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Delete static data';

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
        DB::table('cities')->truncate();
        $this->info('Deleted cities');
        DB::table('divisions')->truncate();
        $this->info('Deleted divisions');
        DB::table('districts')->truncate();
        $this->info('Deleted districts');
        DB::table('upazilas')->truncate();
        $this->info('Deleted upazilas');
        DB::table('food_category')->truncate();
        $this->info('Deleted food categories');
        DB::table('promotion_packages')->truncate();
        $this->info('Deleted promotion packages');
        DB::table('offer_type')->truncate();
        $this->info('Deleted offer types');
        DB::table('promotion_package_prices')->truncate();
        $this->info('Deleted promotion package prices');
    }
}
