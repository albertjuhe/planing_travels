<?php

namespace App\Infrastructure\LocationBundle\Doctrine\Types;

use App\Domain\Location\ValueObject\LocationId;
use Doctrine\DBAL\Platforms\AbstractPlatform;
use Doctrine\DBAL\Types\Type;

class DoctrineLocationIdType extends Type
{
    public function getSQLDeclaration(array $column, AbstractPlatform $platform): string
    {
        return $platform->getGuidTypeDeclarationSQL($column);
    }

    public function convertToDatabaseValue(mixed $value, AbstractPlatform $platform): mixed
    {
        if ($value === null) {
            return null;
        }
        return $value->id();
    }

    public function convertToPHPValue(mixed $value, AbstractPlatform $platform): mixed
    {
        if ($value === null) {
            return null;
        }
        return new LocationId($value);
    }

    public function getName(): string
    {
        return 'LocationId';
    }
}
