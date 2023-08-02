<?php

use ARIA\GraphQLClient\API\WebhookAPI;
use ARIA\GraphQLClient\Client;


class WebhookAPITest extends \PHPUnit\Framework\TestCase {

    private $definition;

    

    public function setUp() :void {

        $client = new Client( $_ENV['ENDPOINT'] );
        $client->setToken( $_ENV['TOKEN'] );
        $this->definition = new WebhookAPI( $client );
        
    
    }


    // Note: actual testing of the endpoint is largely done via postman, this is to ensure I've not stuffed up the GQL

    public function testSubscribers() {

        $results = $this->definition->subscribers(['site_id' => $_ENV['SITE_ID']]);

        $this->assertNotEmpty($results['nodes']);

        $this->assertNotEmpty($results['nodes'][0]['id']);
    }

    public function testAddWebhook() {

        $results = $this->definition->dispatchWebhook(
            'test.webhook',
            [
                'test1' => 'abc',
                'test2' => 'cde'
            ]
        );
        
        $this->assertNotEmpty($results['id']);
    }
    
}