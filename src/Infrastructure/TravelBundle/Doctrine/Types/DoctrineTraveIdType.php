<?php

namespace App\Infrastructure\TravelBundle\Doctrine\Types;

use App\Domain\Travel\ValueObject\TravelId;
use Doctrine\DBAL\Platforms\AbstractPlatform;
use Doctrine\DBAL\Types\Type;

class DoctrineTraveIdType extends Type
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
        return new TravelId($value);
    }

    public function getName(): string
    {
        return 'TravelId';
    }
}
