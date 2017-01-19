<?php
use Laravel\Lumen\Testing\DatabaseMigrations;
use Laravel\Lumen\Testing\DatabaseTransactions;

abstract class JsonTestCase extends Laravel\Lumen\Testing\TestCase
{
  public function createApplication()
  {
      return require __DIR__.'/../bootstrap/app.php';
  }


}
