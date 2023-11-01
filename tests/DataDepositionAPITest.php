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

    public function testBucket() {

        $bucket_id = $this->testCreateDataBucket();

        $this->assertNotEmpty($bucket_id);

        $bucket = $this->definition->bucket($bucket_id);

        $this->assertEquals($bucket_id, $bucket['id']);
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

        $this->assertNotEmpty($field['id']);
        $this->assertIsArray($field['content']);

        return $field;
    }

    public function testRecordFields() {

        $field = $this->testCreateDataField();
        $recordfields = $this->definition->recordFields($field['record']);

        $this->assertEquals($recordfields[0]['id'], $field['id']);
    }
    

    
    
}