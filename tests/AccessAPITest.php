<?php

use ARIA\GraphQLClient\API\AccessAPI;
use ARIA\GraphQLClient\Client;


class AccessAPITest extends \PHPUnit\Framework\TestCase {

    private $definition;

    public function setUp() :void {

        $client = new Client( $_ENV['ENDPOINT'] );
        $client->setToken( $_ENV['TOKEN'] );
        $this->definition = new AccessAPI( $client );
        
    
    }

    public function testUserViewProfile() {

        $result = $this->definition->userViewProfile('test', 'test');

        $this->assertFalse($result);
    }
}