<?php

use ARIA\GraphQLClient\API\AccessAPI;
use ARIA\GraphQLClient\Client;

class AccessAPIVisitItemsTest extends \PHPUnit\Framework\TestCase
{
    private AccessAPI $definition;

    protected function setUp(): void
    {
        $client = new Client($_ENV['ENDPOINT']);
        $client->setToken($_ENV['TOKEN']);

        $this->definition = new AccessAPI($client);
    }

    /**
     * Test fetching visits by proposal_id
     */
    public function testVisitItemsByProposalId(): void
    {
        $result = $this->definition->visitItems([
            'proposal_id' => $_ENV['PROPOSAL_ID']
        ]);

        $this->assertIsArray($result);
        $this->assertNotEmpty($result);

        // Validate expected structure
        $this->assertArrayHasKey('id', $result[0]);
        $this->assertArrayHasKey('proposal_id', $result[0]);
    }

    /**
     * Test fetching visits by visit id
     */
    public function testVisitItemsByVisitId(): void
    {
        $result = $this->definition->visitItems([
            'id' => $_ENV['VISIT_ID']
        ]);

        $this->assertIsArray($result);
        $this->assertNotEmpty($result);

        $this->assertEquals(21, $result[0]['id']);
    }

    /**
     * Test multiple valid filters
     */
    public function testVisitItemsWithMultipleFilters(): void
    {
        $result = $this->definition->visitItems([
            'proposal_id' =>  $_ENV['PROPOSAL_ID'],
            'status' => 'AWAITING_EVALUATION'
        ]);

        $this->assertIsArray($result);
    }

    /**
     * Test invalid filter keys are ignored
     */
    public function testVisitItemsWithInvalidFilter(): void
    {
        $result = $this->definition->visitItems([
            'invalid_field' => 123
        ]);

        // No valid filters → empty result
        $this->assertEmpty($result);
    }

    /**
     * Test empty filters return empty array
     */
    public function testVisitItemsWithEmptyFilters(): void
    {
        $result = $this->definition->visitItems([]);

        $this->assertEmpty($result);
    }
}
