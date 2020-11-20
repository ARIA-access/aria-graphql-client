<?php

use ARIA\GraphQLClient\API\AccessAPI;
use ARIA\GraphQLClient\Client;


class AccessAPITest extends \PHPUnit\Framework\TestCase {

    private $definition;

    public function setUp() :void {

        $this->definition = new AccessAPI( new Client( 'http://localhost:5000/graphql/' ));
    
    }

    public function testUserViewProfile() {

        $result = $this->definition->userViewProfile('test', 'test');

        $this->assertFalse($result);
    }
}