<?php

namespace App\Infrastructure\UserBundle\Doctrine\Types;

use App\Domain\User\ValueObject\UserId;
use Doctrine\DBAL\Platforms\AbstractPlatform;
use Doctrine\DBAL\Types\Type;
use Doctrine\DBAL\Types\Types;

class DoctrineUserIdType extends Type
{
    public function getSQLDeclaration(array $column, AbstractPlatform $platform): string
    {
        return $platform->getIntegerTypeDeclarationSQL($column);
    }

    public function convertToDatabaseValue(mixed $value, AbstractPlatform $platform): mixed
    {
        if ($value instanceof UserId) {
            return $value->id();
        }
        return $value;
    }

    public function convertToPHPValue(mixed $value, AbstractPlatform $platform): mixed
    {
        if ($value === null) {
            return null;
        }
        return new UserId((int) $value);
    }

    public function getName(): string
    {
        return 'UserId';
    }
}
