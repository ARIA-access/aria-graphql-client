<?php

use ARIA\GraphQLClient\API\SiteAPI;
use ARIA\GraphQLClient\Client;


class SiteAPITest extends \PHPUnit\Framework\TestCase {

    private $definition;

    static $site_id;

    public function setUp() :void {

        $this->definition = new SiteAPI( new Client( 'http://localhost:5000/graphql/' ));
    
    }

    public function testSiteDomains() {

        $result = $this->definition->site_domain([
            'domain' => 'localhost'
        ]);
        
        $this->assertNotEmpty($result);

        self::$site_id = $result[0]['site_id'];

    }

    public function testSiteMembers() {

        $result = $this->definition->getMembers(self::$site_id);

        $this->assertNotEmpty($result);
    }

    public function testSiteMembersWithSubmissionOrTeam() {

        $result = $this->definition->getMembersWithSubmissionOrTeam(self::$site_id);

        $this->assertNotEmpty($result);
    }

    public function testAddLog() {

        $result = $this->definition->info(self::$site_id, "I am a log message", [ 'testvalue' => 'context value']);

        $this->assertTrue($result);
    }

    public function testLogs() {

        $logs = $this->definition->logs(['site_id' => self::$site_id]);

        $this->assertNotEmpty($logs);

        $this->assertEquals($logs['nodes'][0]['message'], "I am a log message");
    }
}