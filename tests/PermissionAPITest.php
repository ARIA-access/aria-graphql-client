<?php

use ARIA\GraphQLClient\API\PermissionAPI;
use ARIA\GraphQLClient\Client;


class PermissionAPITest extends \PHPUnit\Framework\TestCase {

    private $definition;

    

    public function setUp() :void {

        $client = new Client( $_ENV['ENDPOINT'] );
        $client->setToken( $_ENV['TOKEN'] );
        $this->definition = new PermissionAPI( $client );
        
    
    }

    public function testProposalPermission() {

        $permissions = $this->definition->proposalPermission(1);

        $this->assertisArray($permissions['scopes'], 'No scopes retrieved');

    }

    
    
}