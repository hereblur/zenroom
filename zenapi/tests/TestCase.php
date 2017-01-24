<?php
use Illuminate\Support\Str;
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

    public function seeRecord(array $data, $negate = false){
      $method = $negate ? 'assertFalse' : 'assertTrue';

      $actual = json_decode($this->response->getContent(), true);

      if (is_null($actual) || $actual === false) {
          return $this->fail('Invalid JSON was returned from the route. Perhaps an exception was thrown?');
      }

      foreach ($actual as $row) {

        $actual_row = json_encode(array_sort_recursive(
            (array) $row
        ));

        $found = true;

        foreach (array_sort_recursive($data) as $key => $value) {
            $expected = $this->formatToExpectedJson($key, $value);

            $found = $found && Str::contains($actual_row, $expected);
        }

        if($found) break;
      }

      $this->{$method}(
          $found,
          ($negate ? 'Found unexpected' : 'Unable to find')." JSON fragment [".json_encode($data)."] within [".$this->response->getContent()."]."
      );

      return $this;
    }

}
