<?php

use PHPUnit\Framework\TestCase;
use ARIA\GraphQLClient\JSONEncodedGQL;


class JSONEncodedGQLTest extends TestCase {


  public function testEncode() {

    $test = [
        'key' => 'value',
        'subkey' => [
            'sub1' => 'val',
            'sub2' => 5
        ]
        ];

    $encoded = JSONEncodedGQL::encode($test);

    $this->assertIsString($encoded);


  }

}