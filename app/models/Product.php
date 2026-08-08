<?php

/** Product model — PDO query wrappers. Schema TBD. */
class Product extends Model
{
    protected string $table = 'products';

    public function all(): array
    {
        return [];
    }

    public function find(int $id): ?array
    {
        return null;
    }
}
