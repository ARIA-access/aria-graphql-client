<?php

use ARIA\GraphQLClient\API\DataDepositionAPI;
use ARIA\GraphQLClient\Client;


class DataDepositionAPITest extends \PHPUnit\Framework\TestCase {

    private DataDepositionAPI $definition;

    

    public function setUp() :void {

        $client = new Client( $_ENV['ENDPOINT'] );
        $client->setToken( $_ENV['TOKEN'] );
        $this->definition = new DataDepositionAPI( $client );
        
    
    }

    public function testCreateDataBucket() {

        $bucket = $this->definition->createDataBucket(1, 'proposal', strtotime('1 January 2050'));

        $this->assertNotEmpty($bucket['id']);

        return $bucket['id'];
    }


    public function testCreateDataRecord() {

        $bucket_id = $this->testCreateDataBucket();

        $this->assertNotEmpty($bucket_id);

        $record = $this->definition->createDataRecord($bucket_id, 'TestSchema');

        $this->assertNotEmpty($record['id']);

        return $record['id'];
    }
    
    public function testCreateDataField() {

        $record_id = $this->testCreateDataRecord();

        $this->assertNotEmpty($record_id);

        $field = $this->definition->createDataField($record_id, 'TestFieldType', ['test' => 'data']);
var_export($field);
        $this->assertNotEmpty($field['id']);
        $this->assertIsArray($field['content']);

    }
    
    
    
}