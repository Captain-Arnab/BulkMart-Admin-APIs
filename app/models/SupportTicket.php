<?php

/** SupportTicket model — PDO query wrappers. Schema TBD. */
class SupportTicket extends Model
{
    protected string $table = 'support_tickets';

    public function all(): array
    {
        return [];
    }

    public function find(int $id): ?array
    {
        return null;
    }
}
