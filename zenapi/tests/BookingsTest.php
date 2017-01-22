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

        $this->post('/api/bookings/'.date("Y-m-d").'/1', ["inventory" => 4])
        ->seeJson(['inventory'=>4])
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
        $this->assertArrayHasKey('inventory', $data[0]);
    }

    public function testBookingsStore()
    {
        $this->post('/api/bookings/'.date("Y-m-d").'/1', ["inventory" => 4])
            ->seeJson(['inventory'=>4])
            ->seeJson(['roomtype'=>1])
            ->seeStatusCode(200);

        $this->post('/api/bookings/'.date("Y-m-d", strtotime("+1day")).'/1', ["inventory" => 3])
            ->seeJson(['inventory'=>3])
            ->seeJson(['roomtype'=>1])
            ->seeStatusCode(200);

        $this->post('/api/bookings/'.date("Y-m-d").'/2', ["inventory" => 2])
            ->seeJson(['inventory'=>2])
            ->seeJson(['roomtype'=>2])
            ->seeStatusCode(200);

        $this->get('/api/bookings/'.date("Y-m"))->seeStatusCode(200);
        $this->assertEquals(3, count($this->getJson()));

        $this->seeRecord(['date'=> date("Y-m-d"), "roomtypeId"=> "1", "roomtypeName"=> "Single room", "inventory"=> "4"]);
        $this->seeRecord(['date'=> date("Y-m-d", strtotime("+1day")), "roomtypeId"=> "1", "roomtypeName"=> "Single room", "inventory"=> "3"]);
        $this->seeRecord(['date'=> date("Y-m-d"), "roomtypeId"=> "2", "roomtypeName"=> "Double room", "inventory"=> "2"]);


        $this->post('/api/bookings/'.date("Y-m-d").'/2', ["inventory" => "5"])
            ->seeJson(['inventory'=>"5"])
            ->seeJson(['roomtype'=>"2"])
            ->seeStatusCode(200);

        $this->post('/api/bookings/'.date("Y-m-d").'/2', ["inventory" => "-5"])
            ->seeJson(['code'=>"500"])
            ->seeStatusCode(500);
        $this->post('/api/bookings/'.date("Y-m-d").'/2', ["inventory" => "A1"])
            ->seeJson(['code'=>"500"])
            ->seeStatusCode(500);


        $this->get('/api/bookings/'.date("Y-m"))->seeStatusCode(200);
        $this->assertEquals(3, count($this->getJson()));

        $this->seeRecord(['date'=> date("Y-m-d"), "roomtypeId"=> "1", "roomtypeName"=> "Single room", "inventory"=> "4"]);
        $this->seeRecord(['date'=> date("Y-m-d", strtotime("+1day")), "roomtypeId"=> "1", "roomtypeName"=> "Single room", "inventory"=> "3"]);
        $this->seeRecord(['date'=> date("Y-m-d"), "roomtypeId"=> "2", "roomtypeName"=> "Double room", "inventory"=> "5"]);

    }

    public function testBookingsBulk()
    {

        $this->post('/api/bookings/bulk', ["roomtype" => 1, "start" => "2017-01-01", "end" => "2017-01-05", "weekdays" => [1,1,1,1,1,1,1], "price" => 400])
          ->seeJson(['saved'=>5, 'created'=>5])
          ->seeStatusCode(200);

        $this->get('/api/bookings/2017-01')->seeStatusCode(200);
        $this->assertEquals(5, count($this->getJson()));
        $this->seeRecord(["date"=>"2017-01-01", "roomtypeId" =>	"1", "inventory"=>"5" , "price"=>"400.00"]);
        $this->seeRecord(["date"=>"2017-01-02", "roomtypeId" =>	"1", "inventory"=>"5" , "price"=>"400.00"]);
        $this->seeRecord(["date"=>"2017-01-03", "roomtypeId" =>	"1", "inventory"=>"5" , "price"=>"400.00"]);
        $this->seeRecord(["date"=>"2017-01-04", "roomtypeId" =>	"1", "inventory"=>"5" , "price"=>"400.00"]);
        $this->seeRecord(["date"=>"2017-01-05", "roomtypeId" =>	"1", "inventory"=>"5" , "price"=>"400.00"]);

        $this->post('/api/bookings/bulk', ["roomtype" => 1, "start" => "2017-01-01", "end" => "2017-01-05", "weekdays" => [1,1,1,1,1,1,1], "price" => 600])
          ->seeJson(['saved'=>5, 'created'=>0])
          ->seeStatusCode(200);

        $this->get('/api/bookings/2017-01')->seeStatusCode(200);
        $this->assertEquals(5, count($this->getJson()));
        $this->seeRecord(["date"=>"2017-01-01", "roomtypeId" =>	"1", "inventory"=>"5" , "price"=>"600.00"]);
        $this->seeRecord(["date"=>"2017-01-02", "roomtypeId" =>	"1", "inventory"=>"5" , "price"=>"600.00"]);
        $this->seeRecord(["date"=>"2017-01-03", "roomtypeId" =>	"1", "inventory"=>"5" , "price"=>"600.00"]);
        $this->seeRecord(["date"=>"2017-01-04", "roomtypeId" =>	"1", "inventory"=>"5" , "price"=>"600.00"]);
        $this->seeRecord(["date"=>"2017-01-05", "roomtypeId" =>	"1", "inventory"=>"5" , "price"=>"600.00"]);

        $this->post('/api/bookings/bulk', ["roomtype" => 1, "start" => "2017-01-01", "end" => "2017-01-16", "weekdays" => [0,1,1,1,1,1,0], "price" => 100])
            ->seeJson(['saved'=>11])
            ->seeStatusCode(200);

        $this->post('/api/bookings/bulk', ["roomtype" => 2, "start" => "2017-01-01", "end" => "2017-01-7", "weekdays" => [1,0,0,1,0,0,1], "inventory" => 10])
            ->seeJson(['saved'=>3])
            ->seeStatusCode(200);

        $this->post('/api/bookings/bulk', ["roomtype" => 1, "start" => "2017-01-08", "end" => "2017-01-16", "weekdays" => [1,0,0,1,0,0,1], "inventory" => 15])
            ->seeJson(['saved'=>4])
            ->seeStatusCode(200);

        $this->get('/api/bookings/2017-01')->seeStatusCode(200);
        $this->assertEquals(18, count($this->getJson()));

        $this->seeRecord(["date"=>"2017-01-01", "roomtypeId" =>	"2", "inventory"=>"10", "price"=>"5000.00"]);
        $this->seeRecord(["date"=>"2017-01-04", "roomtypeId" =>	"2", "inventory"=>"10", "price"=>"5000.00"]);
        $this->seeRecord(["date"=>"2017-01-07", "roomtypeId" =>	"2", "inventory"=>"10", "price"=>"5000.00"]);

        $this->seeRecord(["date"=>"2017-01-01", "roomtypeId" =>	"1", "inventory"=>"5" , "price"=>"600.00"]);
        $this->seeRecord(["date"=>"2017-01-02", "roomtypeId" =>	"1", "inventory"=>"5" , "price"=>"100.00"]);
        $this->seeRecord(["date"=>"2017-01-03", "roomtypeId" =>	"1", "inventory"=>"5" , "price"=>"100.00"]);
        $this->seeRecord(["date"=>"2017-01-04", "roomtypeId" =>	"1", "inventory"=>"5" , "price"=>"100.00"]);
        $this->seeRecord(["date"=>"2017-01-05", "roomtypeId" =>	"1", "inventory"=>"5" , "price"=>"100.00"]);
        $this->seeRecord(["date"=>"2017-01-06", "roomtypeId" =>	"1", "inventory"=>"5" , "price"=>"100.00"]);
        $this->seeRecord(["date"=>"2017-01-08", "roomtypeId" =>	"1", "inventory"=>"15", "price"=>"3000.00"]);
        $this->seeRecord(["date"=>"2017-01-09", "roomtypeId" =>	"1", "inventory"=>"5" , "price"=>"100.00"]);
        $this->seeRecord(["date"=>"2017-01-10", "roomtypeId" =>	"1", "inventory"=>"5" , "price"=>"100.00"]);
        $this->seeRecord(["date"=>"2017-01-11", "roomtypeId" =>	"1", "inventory"=>"15" , "price"=>"100.00"]);
        $this->seeRecord(["date"=>"2017-01-12", "roomtypeId" =>	"1", "inventory"=>"5" , "price"=>"100.00"]);
        $this->seeRecord(["date"=>"2017-01-13", "roomtypeId" =>	"1", "inventory"=>"5" , "price"=>"100.00"]);
        $this->seeRecord(["date"=>"2017-01-14", "roomtypeId" =>	"1", "inventory"=>"15", "price"=>"3000.00"]);
        $this->seeRecord(["date"=>"2017-01-16", "roomtypeId" =>	"1", "inventory"=>"5" , "price"=>"100.00"]);
        $this->seeRecord(["date"=>"2017-01-15", "roomtypeId" =>	"1", "inventory"=>"15", "price"=>"3000.00"]);

    }


}
