<?php

use ARIA\GraphQLClient\API\SiteAPI;
use ARIA\GraphQLClient\Client;


class SiteAPITest extends \PHPUnit\Framework\TestCase {

    private $definition;

    public function setUp() :void {

        $this->definition = new SiteAPI( new Client( 'http://localhost:5000/graphql/' ));
    
    }

    public function testSiteDomains() {

        $result = $this->definition->site_domain([
            'domain' => 'localhost'
        ]);
        
        $this->assertNotEmpty($result);

    }
}