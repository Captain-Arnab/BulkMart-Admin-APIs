<?php

/** MarketPrice model — PDO query wrappers. Schema TBD. */
class MarketPrice extends Model
{
    protected string $table = 'market_prices';

    public function all(): array
    {
        return [];
    }

    public function find(int $id): ?array
    {
        return null;
    }
}
