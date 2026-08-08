<?php

class NotificationService
{
    public static function notifyCustomer(int $customerId, string $title, string $body, string $type = 'verification', ?int $relatedId = null): void
    {
        $stmt = db()->prepare(
            'INSERT INTO notifications (customer_id, title, body, type, related_id, is_read) VALUES (?,?,?,?,?,0)'
        );
        $stmt->execute([$customerId, $title, $body, $type, $relatedId]);
    }
}
