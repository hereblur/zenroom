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
        DB::table('roomtypes')->insert([ 'type' => "Single room", 'inventory' => 5, 'baseprice' => 3000 ]);
        DB::table('roomtypes')->insert([ 'type' => "Double room", 'inventory' => 5, 'baseprice' => 5000 ]);
    }
}
