<?php

/** Offer model — PDO query wrappers. Schema TBD. */
class Offer extends Model
{
    protected string $table = 'offers';

    public function all(): array
    {
        return [];
    }

    public function find(int $id): ?array
    {
        return null;
    }
}
