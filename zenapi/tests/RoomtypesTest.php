<?php

use Laravel\Lumen\Testing\DatabaseMigrations;
use Laravel\Lumen\Testing\DatabaseTransactions;

class RoomtypesTest extends TestCase
{
    use DatabaseMigrations;

    public function testSimpleRoute()
    {
        $this->get('/');

        $this->assertEquals(
            $this->app->version(), $this->response->getContent()
        );
    }

    public function testRoomtypeIndex()
    {
        $this->get('/api/roomtypes')
            ->seeJson()
            ->seeJsonArray()
            ->seeStatusCode(200);

        $data = $this->getJson();

        $this->assertEquals(count($data), 2);
        $this->assertArrayHasKey('id', $data[0]);
        $this->assertArrayHasKey('type', $data[0]);
        $this->assertArrayHasKey('baseprice', $data[0]);
        $this->assertArrayHasKey('id', $data[1]);
        $this->assertArrayHasKey('type', $data[1]);
        $this->assertArrayHasKey('baseprice', $data[1]);
    }

    public function testRoomtypeCreate()
    {
        $this->put('/api/roomtypes', ["type" => "Double room", "baseprice" => 1000])
            ->seeJson(['code'=>'409'])
            ->seeStatusCode(409);

        $this->put('/api/roomtypes', ["type" => "Triple room", "baseprice" => -1000])
            ->seeJson(['code'=>'409'])
            ->seeStatusCode(409);

        $this->put('/api/roomtypes', ["type" => "Triple room", "baseprice" => 0])
            ->seeJson(['code'=>'409'])
            ->seeStatusCode(409);

        $this->put('/api/roomtypes', ["type" => "Triple room"])
            ->seeJson(['code'=>'409'])
            ->seeStatusCode(409);

        $this->put('/api/roomtypes', ["type" => "Triple room", "baseprice" => 1000])
            ->seeJson(['type'=>'Triple room'])
            ->seeStatusCode(200);


        $this->get('/api/roomtypes')
            ->seeJson()
            ->seeJsonArray()
            ->seeStatusCode(200);

        $data = $this->getJson();

        $this->assertEquals(count($data), 3);
    }

    public function testRoomtypeUpdate()
    {
        $this->post('/api/roomtypes/1', ["type" => "Double room", "baseprice" => 8000])
            ->seeJson(['code'=>'409'])
            ->seeStatusCode(409);

        $this->post('/api/roomtypes/1', ["type" => "Triple room", "baseprice" => -1000])
            ->seeJson(['code'=>'409'])
            ->seeStatusCode(409);

        $this->post('/api/roomtypes/1', ["type" => "Triple room", "baseprice" => 0])
            ->seeJson(['code'=>'409'])
            ->seeStatusCode(409);

        $this->post('/api/roomtypes/1', ["type" => "Triple room"])
            ->seeJson(['type'=>'Triple room'])
            ->seeStatusCode(200);

        $this->post('/api/roomtypes/1', ["baseprice" => 4000])
            ->seeJson(['type'=>'Triple room'])
            ->seeStatusCode(200);

        $this->get('/api/roomtypes')
            ->seeJson()
            ->seeJsonArray()
            ->seeStatusCode(200)
            ->seeRecord(['id'=> 1, "type"=> "Triple room", "baseprice"=> "4000"]);

        $this->assertEquals(count($this->getJson()), 2);
    }
}
