<?php

use Illuminate\Database\Seeder;
use Carbon\Carbon;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     *
     * @return void
     */
    public function run()
    {
        $arr = array();
        for($i = 0; $i < 50000; $i++){
        $date = Carbon::create(2018, 1, 1, 0, 0, 0);
        $arr[] = $i;
            DB::table('employees')->insert([
                'parent_id' => array_rand($arr),
                'name' => "Employee_".str_random(5),
                'position' => str_random(10),
                'hire_date' => $date->addWeeks(rand(1, 52))->format('Y-m-d H:i:s'),
                'salary' => rand(10000, 50000)
            ]);
        }
    }
}
