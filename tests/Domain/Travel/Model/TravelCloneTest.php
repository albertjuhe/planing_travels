<?php

namespace App\Tests\Domain\Travel\Model;

use App\Domain\Travel\Model\TravelClone;
use App\Tests\Domain\User\Model\UserMother;
use PHPUnit\Framework\TestCase;

class TravelCloneTest extends TestCase
{
    public function testConstructorSetsAllFields(): void
    {
        $target = TravelMother::random();
        $cloner = UserMother::random();

        $clone = new TravelClone(
            'source-travel-uuid',
            42,
            'Original Trip Title',
            $target,
            $cloner,
            1
        );

        $this->assertNotEmpty($clone->getId());
        $this->assertMatchesRegularExpression(
            '/^[0-9a-f]{8}-[0-9a-f]{4}-4[0-9a-f]{3}-[89ab][0-9a-f]{3}-[0-9a-f]{12}$/i',
            $clone->getId()
        );
        $this->assertSame('source-travel-uuid', $clone->getSourceTravelId());
        $this->assertSame(42, $clone->getSourceUserId());
        $this->assertSame('Original Trip Title', $clone->getSourceTitleSnapshot());
        $this->assertSame($target, $clone->getTargetTravel());
        $this->assertSame($cloner, $clone->getClonedByUser());
        $this->assertSame(1, $clone->getDepth());
        $this->assertInstanceOf(\DateTime::class, $clone->getClonedAt());
    }

    public function testDepthDefaultsToOne(): void
    {
        $clone = new TravelClone(
            'source-id',
            1,
            'Title',
            TravelMother::random(),
            UserMother::random()
        );

        $this->assertSame(1, $clone->getDepth());
    }

    public function testDepthTwoForCloneOfClone(): void
    {
        $clone = new TravelClone(
            'source-id',
            1,
            'Title',
            TravelMother::random(),
            UserMother::random(),
            2
        );

        $this->assertSame(2, $clone->getDepth());
    }

    public function testSourceTitleSnapshotPersistsIndependentlyOfSource(): void
    {
        $clone = new TravelClone(
            'source-id',
            1,
            'Snapshot Title',
            TravelMother::random(),
            UserMother::random()
        );

        $this->assertSame('Snapshot Title', $clone->getSourceTitleSnapshot());
    }

    public function testTwoClonesHaveDifferentIds(): void
    {
        $c1 = new TravelClone('s', 1, 'T', TravelMother::random(), UserMother::random());
        $c2 = new TravelClone('s', 1, 'T', TravelMother::random(), UserMother::random());

        $this->assertNotSame($c1->getId(), $c2->getId());
    }
}
