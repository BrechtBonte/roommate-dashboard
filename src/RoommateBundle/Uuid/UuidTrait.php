<?php

namespace RoommateBundle\Uuid;

use Rhumsaa\Uuid\Uuid;

trait UuidTrait
{
    /** @var \Rhumsaa\Uuid\Uuid */
    private $uuid;

    public function __construct(Uuid $uuid)
    {
        $this->uuid = $uuid;
    }

    public static function generate()
    {
        return new static(Uuid::uuid1());
    }

    public static function fromString($uuid)
    {
        return new static(Uuid::fromString($uuid));
    }

    /** @return bool */
    public function equals($uuid)
    {
        return $uuid instanceof static && $this->uuid->equals($uuid->uuid);
    }

    public function __toString()
    {
        return (string) $this->uuid;
    }
}
