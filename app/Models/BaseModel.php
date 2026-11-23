<?php
declare(strict_types=1);

abstract class BaseModel
{
    public function __construct(protected PDO $db)
    {
    }

    abstract public function getAll(): array;
}
