<?php

use Laravel\Lumen\Testing\DatabaseMigrations;
use Laravel\Lumen\Testing\DatabaseTransactions;

class BookingsTest extends TestCase
{
    use DatabaseMigrations;

    public function testBookingsIndex()
    {
        $this->get('/api/bookings/'.date("Y-m"))
          ->seeJson()
          ->seeJsonArray()
          ->seeStatusCode(200);

        $this->assertEquals(count($this->getJson()), 0);

        $this->post('/api/bookings/'.date("Y-m-d").'/1', ["occupied" => 4])
        ->seeJson(['occupied'=>4])
        ->seeStatusCode(200);

        $this->get('/api/bookings/'.date("Y-m"))
        ->seeJson()
        ->seeJsonArray()
        ->seeStatusCode(200);

        $data = $this->getJson();

        $this->assertEquals(count($data), 1);

        $this->assertArrayHasKey('date', $data[0]);
        $this->assertArrayHasKey('roomtypeName', $data[0]);
        $this->assertArrayHasKey('roomtypeId', $data[0]);
        $this->assertArrayHasKey('occupied', $data[0]);
    }

    public function testBookingsStore()
    {
        $this->post('/api/bookings/'.date("Y-m-d").'/1', ["occupied" => 4])
            ->seeJson(['occupied'=>4])
            ->seeJson(['roomtype'=>1])
            ->seeStatusCode(200);

        $this->post('/api/bookings/'.date("Y-m-d", strtotime("+1day")).'/1', ["occupied" => 3])
            ->seeJson(['occupied'=>3])
            ->seeJson(['roomtype'=>1])
            ->seeStatusCode(200);

        $this->post('/api/bookings/'.date("Y-m-d").'/2', ["occupied" => 2])
            ->seeJson(['occupied'=>2])
            ->seeJson(['roomtype'=>2])
            ->seeStatusCode(200);

        $this->get('/api/bookings/'.date("Y-m"))->seeStatusCode(200);
        $this->assertEquals(3, count($this->getJson()));

        $this->seeRecord(['date'=> date("Y-m-d"), "roomtypeId"=> 1, "roomtypeName"=> "Single room", "occupied"=> 4]);
        $this->seeRecord(['date'=> date("Y-m-d", strtotime("+1day")), "roomtypeId"=> 1, "roomtypeName"=> "Single room", "occupied"=> 3]);
        $this->seeRecord(['date'=> date("Y-m-d"), "roomtypeId"=> 2, "roomtypeName"=> "Double room", "occupied"=> 2]);


        $this->post('/api/bookings/'.date("Y-m-d").'/2', ["occupied" => 5])
            ->seeJson(['occupied'=>5])
            ->seeJson(['roomtype'=>2])
            ->seeStatusCode(200);

        $this->post('/api/bookings/'.date("Y-m-d").'/2', ["occupied" => "-5"])
            ->seeJson(['code'=>"500"])
            ->seeStatusCode(500);
        $this->post('/api/bookings/'.date("Y-m-d").'/2', ["occupied" => "A1"])
            ->seeJson(['code'=>"500"])
            ->seeStatusCode(500);


        $this->get('/api/bookings/'.date("Y-m"))->seeStatusCode(200);
        $this->assertEquals(3, count($this->getJson()));


        $this->seeRecord(['date'=> date("Y-m-d"), "roomtypeId"=> 1, "roomtypeName"=> "Single room", "occupied"=> 4]);
        $this->seeRecord(['date'=> date("Y-m-d", strtotime("+1day")), "roomtypeId"=> 1, "roomtypeName"=> "Single room", "occupied"=> 3]);
        $this->seeRecord(['date'=> date("Y-m-d"), "roomtypeId"=> 2, "roomtypeName"=> "Double room", "occupied"=> 5]);

    }
}
