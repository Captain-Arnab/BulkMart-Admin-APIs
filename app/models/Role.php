<?php

/** Role model — PDO query wrappers. Schema TBD. */
class Role extends Model
{
    protected string $table = 'roles';

    public function all(): array
    {
        return [];
    }

    public function find(int $id): ?array
    {
        return null;
    }
}
