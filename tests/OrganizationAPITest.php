<?php

use ARIA\GraphQLClient\API\OrganizationAPI;
use ARIA\GraphQLClient\Client;


class OrganizationAPITest extends \PHPUnit\Framework\TestCase {

    private $definition;

    public function setUp() :void {

        $client = new Client( $_ENV['ENDPOINT'] );
        $client->setToken( $_ENV['TOKEN'] );
        $this->definition = new OrganizationAPI( $client );
    
    }

    public function testOrganisation() {

        $result = $this->definition->organization();

        $this->assertTrue(count($result) > 0);

        $this->assertIsString($result[0]['id']);
    }

    public function testOrganisationSearch() {

        $result = $this->definition->search();
         
        $this->assertTrue(count($result) > 0);

        $this->assertIsString($result['nodes'][0]['id']);
    }
    
}