<?php

declare(strict_types=1);

namespace App\Model;

class User
{
    public const TYPE_PRIVATE = 'private';
    public const TYPE_BUSINESS = 'business';

    /**
     * @var int
     */
    protected $id;

    /**
     * @var string
     */
    protected $type;

    public function __construct(int $id = null, string $type = null)
    {
        $this->id = $id;
        $this->type = $type;
    }

    /**
     * @return int
     */
    public function getId(): int
    {
        return $this->id;
    }

    /**
     * @param int $id
     */
    public function setId(int $id)
    {
        $this->id = $id;
    }

    /**
     * @return string
     */
    public function getType(): string
    {
        return $this->type;
    }

    /**
     * @param string $type
     */
    public function setType(string $type)
    {
        $this->type = $type;
    }

    public function isPrivate(): bool
    {
        return $this->type === self::TYPE_PRIVATE;
    }

    public function isBusiness(): bool
    {
        return $this->type === self::TYPE_BUSINESS;
    }
}
