<?php

use ARIA\GraphQLClient\API\AccessAPI;
use ARIA\GraphQLClient\Client;

class AccessAPIProposalItemsTest extends \PHPUnit\Framework\TestCase
{
    private AccessAPI $definition;

    protected function setUp(): void
    {
        $client = new Client($_ENV['ENDPOINT']);
        $client->setToken($_ENV['TOKEN']);

        $this->definition = new AccessAPI($client);
    }

    /**
     * Test fetching proposals by username
     */
    public function testProposalItemsByUsername(): void
    {
        $result = $this->definition->proposalItems([
            'username' => $_ENV['USERNAME']
        ]);

        $this->assertIsArray($result);
        $this->assertNotEmpty($result);

        $this->assertArrayHasKey('id', $result[0]);
        $this->assertArrayHasKey('username', $result[0]);
    }

    /**
     * Test multiple valid filters
     */
    public function testProposalItemsWithMultipleFilters(): void
    {
        $result = $this->definition->proposalItems([
            'username' => $_ENV['USERNAME'],
            'status'   => 'SUBMITTED'
        ]);

        $this->assertIsArray($result);
    }

    /**
     * Test invalid filter keys are ignored
     */
    public function testProposalItemsWithInvalidFilter(): void
    {
        $result = $this->definition->proposalItems([
            'invalid_field' => 123
        ]);

        // No valid filters → empty result
        $this->assertEmpty($result);
    }

    /**
     * Test empty filters return empty array
     */
    public function testProposalItemsWithEmptyFilters(): void
    {
        $result = $this->definition->proposalItems([]);

        $this->assertEmpty($result);
    }
}