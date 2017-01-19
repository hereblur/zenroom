<?php

abstract class TestCase extends Laravel\Lumen\Testing\TestCase
{


    /**
     * Creates the application.
     *
     * @return \Laravel\Lumen\Application
     */
    public function createApplication()
    {
      return require __DIR__.'/../bootstrap/app.php';
    }

    public function setUp(){

      parent::setUp();

      $this->artisan('migrate');
      $this->artisan('db:seed');
    }

    public function getJson(){
      return json_decode($this->response->getContent(), true);
    }

    public function seeJsonArray(){
      $this->assertJson(
        $this->response->getContent(), "JSON was not returned from [{$this->currentUri}]."
      );

      $actual = json_decode($this->response->getContent(), true);

      $this->assertTrue( count($actual)==0 || array_keys($actual) === range(0, count($actual) - 1) );

      return $this;
    }

    public function seeRecord($record){
      $this->assertJson(
        $this->response->getContent(), "JSON was not returned from [{$this->currentUri}]."
      );

      $actual = json_decode($this->response->getContent(), true);

      $this->assertTrue( count($actual)==0 || array_keys($actual) === range(0, count($actual) - 1) );

      return $this;
    }

}
