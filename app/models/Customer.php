<?php

class Customer extends Model
{
    public const KYC_BADGE = [
        'pending'  => 'bg-warning text-dark',
        'approved' => 'bg-success',
        'rejected' => 'bg-danger',
    ];

    public const DOC_LABELS = [
        'gst_certificate'     => 'GST Certificate',
        'fssai_license'       => 'FSSAI License',
        'pan_card'            => 'PAN Card',
        'aadhaar_card'        => 'Aadhaar Card',
        'shop_establishment'  => 'Shop Establishment',
        'trade_license'       => 'Trade License',
        'cancelled_cheque'    => 'Cancelled Cheque',
        'business_photo'      => 'Business Photo',
        'owner_photo'         => 'Owner Photo',
    ];

    public function paginate(array $filters, int $page = 1, int $perPage = 15): array
    {
        $where = ['1=1'];
        $params = [];
        if (!empty($filters['kyc_status'])) {
            $where[] = 'kyc_status = ?';
            $params[] = $filters['kyc_status'];
        }
        if (!empty($filters['q'])) {
            $where[] = '(business_name LIKE ? OR owner_name LIKE ? OR mobile LIKE ?)';
            $like = '%' . $filters['q'] . '%';
            array_push($params, $like, $like, $like);
        }
        $sqlWhere = implode(' AND ', $where);
        $total = (int) ($this->fetchOne("SELECT COUNT(*) AS c FROM customers WHERE $sqlWhere", $params)['c'] ?? 0);
        $pages = max(1, (int) ceil($total / $perPage));
        $page = max(1, min($page, $pages));
        $offset = ($page - 1) * $perPage;
        $rows = $this->fetchAll(
            "SELECT * FROM customers WHERE $sqlWhere ORDER BY created_at DESC LIMIT $perPage OFFSET $offset",
            $params
        );
        return compact('rows', 'total', 'page', 'pages') + ['per_page' => $perPage];
    }

    public function find(int $id): ?array
    {
        return $this->fetchOne('SELECT * FROM customers WHERE id = ?', [$id]);
    }

    public function documents(int $customerId): array
    {
        return $this->fetchAll(
            'SELECT * FROM customer_documents WHERE customer_id = ? ORDER BY uploaded_at DESC',
            [$customerId]
        );
    }

    public function addresses(int $customerId): array
    {
        return $this->fetchAll(
            'SELECT * FROM addresses WHERE customer_id = ? ORDER BY is_default DESC, id ASC',
            [$customerId]
        );
    }

    public function orders(int $customerId, int $limit = 20): array
    {
        return $this->fetchAll(
            'SELECT id, order_number, status, total, placed_at FROM orders WHERE customer_id = ? ORDER BY placed_at DESC LIMIT ' . (int) $limit,
            [$customerId]
        );
    }

    public function updateKyc(int $id, string $status, ?string $reason = null): bool
    {
        return $this->execute(
            'UPDATE customers SET kyc_status = ?, kyc_rejection_reason = ? WHERE id = ?',
            [$status, $reason, $id]
        );
    }

    public function setBlocked(int $id, bool $blocked): bool
    {
        return $this->execute('UPDATE customers SET is_blocked = ? WHERE id = ?', [$blocked ? 1 : 0, $id]);
    }
}
