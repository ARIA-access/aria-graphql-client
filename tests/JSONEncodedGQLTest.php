<?php

use PHPUnit\Framework\TestCase;
use ARIA\GraphQLClient\JSONEncodedGQL;


class JSONEncodedGQLTest extends TestCase {


  public function testEncode() {

    $obj = new stdClass;
    $obj->foo = "bar";

    $test = [
        'key' => 'value',
        'subkey' => [
            'sub1' => 'val',
            'sub2' => 5,
            'sub3' => $obj,
        ], 
        'subarray' => [ 'value', 'value2', 'value3']
        ];

    $encoded = JSONEncodedGQL::encode($test);

    echo $encoded;
    $this->assertIsString($encoded);


  }

}