<?php
/**
 * Created by PhpStorm.
 * User: albert.juhe
 * Date: 18/10/2018
 * Time: 08:06
 */

namespace App\Tests\Model;

use App\Domain\Gpx\Model\Gpx;
use PHPUnit\Framework\TestCase;
use App\Domain\Travel\Model\Travel;

class GpxTest extends TestCase
{

    /** @var Gpx */
    private $gpx;
    /** @var Travel */
    private $travel;

    public function setUp() {
        $this->gpx = new Gpx();
        $this->gpx->setId(1);
        $this->gpx->setTitle('title-gpx');
        $this->gpx->setDescription('description-gpx');
        $this->gpx->setFilename('filename-gpx.gpx');
        $this->gpx->setColor('red');
        $this->gpx->setCreatedAt(new \DateTime('2018-01-01'));
        $this->gpx->setUpdatedAt(new \DateTime('2018-01-01'));
        $this->travel = new Travel();
        $this->travel->setId(1234);
        $this->gpx->setTravel($this->travel);

    }

    public function testSettersGetters() {
        $this->assertEquals(1,$this->gpx->getId());
        $this->assertEquals('title-gpx',$this->gpx->getTitle());
        $this->assertEquals('filename-gpx.gpx',$this->gpx->getFilename());
        $this->assertEquals('description-gpx',$this->gpx->getDescription());
        $this->assertEquals('red',$this->gpx->getColor());
        $this->assertEquals(new \DateTime('2018-01-01'),$this->gpx->getCreatedAt());
        $this->assertEquals(new \DateTime('2018-01-01'),$this->gpx->getUpdatedAt());
        $this->assertTrue($this->gpx->getTravel()->equals($this->travel));
    }
}