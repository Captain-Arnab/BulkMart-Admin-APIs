<?php

/** Order model — PDO query wrappers. Schema TBD. */
class Order extends Model
{
    protected string $table = 'orders';

    public function all(): array
    {
        return [];
    }

    public function find(int $id): ?array
    {
        return null;
    }
}
