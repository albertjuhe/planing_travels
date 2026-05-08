<?php

namespace App\Infrastructure\LocationBundle\Doctrine\Types;

use App\Domain\Location\ValueObject\LocationId;
use Doctrine\DBAL\Platforms\AbstractPlatform;
use Doctrine\DBAL\Types\GuidType;

class DoctrineLocationIdType extends GuidType
{
    public function getName()
    {
        return 'LocationId';
    }

    public function convertToDatabaseValue($value, AbstractPlatform $platform)
    {
        if ($value === null) {
            return null;
        }
        return $value->id();
    }

    public function convertToPHPValue($value, AbstractPlatform $platform)
    {
        if ($value === null) {
            return null;
        }
        return new LocationId($value);
    }
}
