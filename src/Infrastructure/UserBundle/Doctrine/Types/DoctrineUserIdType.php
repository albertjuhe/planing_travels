<?php


namespace App\Infrastructure\UserBundle\Doctrine\Types;


use App\Domain\User\ValueObject\UserId;
use Doctrine\DBAL\Platforms\AbstractPlatform;
use Doctrine\DBAL\Types\IntegerType;

class DoctrineUserIdType extends IntegerType
{
    public function getName()
    {
        return 'UserId';
    }

    public function convertToDatabaseValue($value, AbstractPlatform $platform)
    {
        return $value->id();
    }

    public function convertToPHPValue($value, AbstractPlatform $platform)
    {
        return new UserId($value);
    }
}