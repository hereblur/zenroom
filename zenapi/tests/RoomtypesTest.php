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
        $this->assertArrayHasKey('id', $data[1]);
        $this->assertArrayHasKey('type', $data[1]);
    }

    public function testRoomtypeCreate()
    {
        $this->put('/api/roomtypes', ["type" => "Big"])
            ->seeJson(['code'=>'409'])
            ->seeStatusCode(409);

        $this->put('/api/roomtypes', ["type" => "Large"])
            ->seeJson(['type'=>'Large'])
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
        $this->post('/api/roomtypes/1', ["type" => "Big"])
            ->seeJson(['code'=>'409'])
            ->seeStatusCode(409);

        $this->post('/api/roomtypes/1', ["type" => "Large"])
            ->seeJson(['type'=>'Large'])
            ->seeStatusCode(200);

        $this->get('/api/roomtypes')
            ->seeJson()
            ->seeJsonArray()
            ->seeStatusCode(200);

        $data = $this->getJson();

        foreach ($data as $room) {
            if ($room["id"] == 1) {
                $this->assertEquals($room['type'], 'Large');
            }
        }
        $this->assertEquals(count($data), 2);
    }
}
