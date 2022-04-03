<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use App\Models\FoodCategory;
use App\Models\Setting;
use Carbon\Carbon;

class ConfigureLevia extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'levia:config';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Feeds essential data to database like districts, categories etc';

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
     * @return void
     */
    public function handle()
    {
        DB::insert("INSERT INTO `cities` (`city_id`, `city_name`) VALUES
            (1, 'Chittagong'),
            (2, 'Dhaka'),
            (3, 'Barisal'),
            (4, 'Khulna'),
            (5, 'Rajshahi'),
            (6, 'Tangail'),
            (7, 'Sunamgonj'),
            (8, 'Comilla'),
            (9, 'Jessore')
        ");
        $this->info('Inserted Districts');

        $this->info("Checking Divisions Table --- ");
        $divisions = json_decode(file_get_contents(base_path() .'/resources/json/bd-divisions.json'), true);
        if (DB::table('divisions')->count() < count($divisions['divisions'])){
            $this->info("Clearing Table --- ");
            DB::table('divisions')->truncate();
            $this->info("Inserting Data ---");
            $data_divisions = [];
            foreach($divisions['divisions'] as $division){
                $data_divisions[] = [
                    'id' => $division['id'],
                    'name' => $division['name'],
                    'bn_name' => $division['bn_name']
                ];
            }
            DB::table('divisions')->insert($data_divisions);
            $this->info("Done\r\n");
        } else {
            $this->info("Table is Complete. Skipping\r\n");
        }

        // Insert Districts if Doesn't Exist on Database
        $this->info("Checking Districts Table --- ");
        $districts = json_decode(file_get_contents(base_path() .'/resources/json/bd-districts.json'), true);
        if (DB::table('districts')->count() < count($districts['districts'])){
            $this->info("Clearing Table --- ");
            DB::table('districts')->truncate();
            $this->info("Inserting Data ---");
            $data_districts = [];
            foreach($districts['districts'] as $district){
                $data_districts[] = [
                    'district_id' => $district['id'],
                    'district_name' => $district['name'],
                    'division_id' => $district['division_id']
                ];
            }
            DB::table('districts')->insert($data_districts);
            $this->info("Done\r\n");
        } else {
            $this->info("Table is Complete. Skipping\r\n");
        }

        // Insert Upazilas if Doesn't Exist on Database
        $this->info("Checking Upazilas Table --- ");
        $upazilas = json_decode(file_get_contents(base_path() .'/resources/json/bd-upazilas.json'), true);
        if (DB::table('upazilas')->count() < count($upazilas['upazilas'])){
            $this->info("Clearing Table --- ");
            DB::table('upazilas')->truncate();
            $this->info("Inserting Data ---");
            DB::table('upazilas')->insert($upazilas['upazilas']);
            $this->info("Done\r\n");
        } else {
            $this->info("Table is Complete. Skipping\r\n");
        }

        DB::insert("INSERT INTO `promotion_packages` (`name`) VALUES
            ('Sponsored Ad')
        ");
        $this->info('Inserted Promotion Packages');

        DB::insert("INSERT INTO `notification_type` (`id`, `type`) VALUES
            (1, 'local'),
            (2, 'global')
        ");
        $this->info('Inserted Notification Types');

        DB::insert("INSERT INTO `promotion_package_prices` (`package_id`, `duration`, `price`) VALUES
            (1, 3, 500),
            (1, 5, 800),
            (1, 7, 1000),
            (1, 10, 1200),
            (1, 15, 1500)
        ");
        $this->info('Inserted Promotion Package Prices');

        DB::insert("INSERT INTO `offer_type` (`offer_type_id`, `offer_type_name`, `created_at`, `updated_at`) VALUES
        (1, 'Discount x%', NULL, NULL),
        (2, 'Discount x tk', NULL, NULL),
        (3, 'Free delivery', NULL, NULL),
        (4, 'Buy X get Y discount', NULL, NULL),
        (5, 'Spent X get Y', NULL, NULL),
        (6, 'X Tk offer', NULL, NULL)");
        $this->info('Inserted Offer Types');

        factory(FoodCategory::class, 10)->create();
        $this->info('Inserted Food Categories');

        Setting::create([
            'key' => 'delivery_fee',
            'value' => "0",
            'created_at' => Carbon::now(),
            'updated_at' => Carbon::now(),
        ]);
        $this->info('Inserted Setting("delivery_fee"=>"0")');

        Setting::create([
            'key' => 'commission_rate',
            'value' => "0",
            'value_type' => 'percent',
            'created_at' => Carbon::now(),
            'updated_at' => Carbon::now(),
        ]);
        $this->info('Inserted Setting("commission_rate"=>"0")');
    }
}
