<?php

/** Customer model — PDO query wrappers. Schema TBD. */
class Customer extends Model
{
    protected string $table = 'customers';

    public function all(): array
    {
        return [];
    }

    public function find(int $id): ?array
    {
        return null;
    }
}
