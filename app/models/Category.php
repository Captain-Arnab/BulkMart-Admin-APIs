<?php

/** Category model — PDO query wrappers. Schema TBD. */
class Category extends Model
{
    protected string $table = 'categories';

    public function all(): array
    {
        return [];
    }

    public function find(int $id): ?array
    {
        return null;
    }
}
