<?php
/**
 * Created by Hugues
 */

namespace App\Tests\Unit\Service;

use App\Service\YamlStatsProvider;
use PHPUnit\Framework\TestCase;
use Symfony\Component\Yaml\Exception\DumpException;
use Symfony\Component\Yaml\Exception\ParseException;
use Symfony\Component\Yaml\Yaml;

class YamlStatsProviderTest extends TestCase
{

    public $stats = array();
    public $yamlProvider;

    public function setUp()
    {
        $this->yamlProvider = new YamlStatsProvider();
        $this->stats = $this->yamlProvider->getStats();
    }

    public function testSetStats() {

        $this->yamlProvider = new YamlStatsProvider();
        $this->yamlProvider->setStats($this->stats);

        $this->assertEquals($this->stats, $this->yamlProvider->getStats());

    }

    public function testGetStats()
    {
        $this->yamlProvider = new YamlStatsProvider();
        $statsFromFile = $this->yamlProvider->getStats();

        $this->assertTrue(!empty($statsFromFile));
        $this->assertSame($this->stats, $statsFromFile);

    }

}