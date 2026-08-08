<?php

/** Banner model — PDO query wrappers. Schema TBD. */
class Banner extends Model
{
    protected string $table = 'banners';

    public function all(): array
    {
        return [];
    }

    public function find(int $id): ?array
    {
        return null;
    }
}
