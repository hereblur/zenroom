<?php

use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
     public function run()
     {
         $this->roomsType();
     }

    private function roomsType()
    {
        DB::table('roomtypes')->insert([ 'type' => "Small", 'inventory' => 5 ]);
        DB::table('roomtypes')->insert([ 'type' => "Big", 'inventory' => 5 ]);
    }
}
