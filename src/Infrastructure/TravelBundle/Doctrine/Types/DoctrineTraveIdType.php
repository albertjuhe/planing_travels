<?php

namespace App\Infrastructure\TravelBundle\Doctrine\Types;

use App\Domain\Travel\ValueObject\TravelId;
use Doctrine\DBAL\Platforms\AbstractPlatform;
use Doctrine\DBAL\Types\GuidType;

class DoctrineTraveIdType extends GuidType
{
    public function getName()
    {
        return 'TravelId';
    }

    public function convertToDatabaseValue($value, AbstractPlatform $platform)
    {
        return $value->id();
    }

    public function convertToPHPValue($value, AbstractPlatform $platform)
    {
        return new TravelId($value);
    }
}
