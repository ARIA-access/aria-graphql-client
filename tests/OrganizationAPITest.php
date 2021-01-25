<?php

use ARIA\GraphQLClient\API\OrganizationAPI;
use ARIA\GraphQLClient\Client;


class OrganizationAPITest extends \PHPUnit\Framework\TestCase {

    private $definition;

    public function setUp() :void {

        $this->definition = new OrganizationAPI( new Client( 'http://localhost:5000/graphql/' ));
    
    }

    public function testSiteDomains() {

        $result = $this->definition->organization();
            
        $this->assertTrue(count($result) > 0);

        $this->assertIsString($result[0]['id']);
    }
    
}