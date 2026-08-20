<?php

class BulkEnquiry extends Model
{
    public const STATUSES = ['new', 'contacted', 'quoted', 'closed'];

    public const STATUS_BADGE = [
        'new'       => 'bg-warning text-dark',
        'contacted' => 'bg-info text-dark',
        'quoted'    => 'text-white',
        'closed'    => 'bg-success',
    ];

    public const STATUS_STYLE = [
        'quoted' => 'background:#7c3aed',
    ];

    public function paginate(array $filters, int $page = 1, int $perPage = 20): array
    {
        $where = ['1=1'];
        $params = [];

        if (!empty($filters['status'])) {
            $where[] = 'e.status = ?';
            $params[] = $filters['status'];
        }
        if (!empty($filters['q'])) {
            $where[] = '(e.name LIKE ? OR e.business_name LIKE ? OR e.mobile LIKE ? OR p.name LIKE ? OR e.delivery_location LIKE ?)';
            $like = '%' . $filters['q'] . '%';
            array_push($params, $like, $like, $like, $like, $like);
        }

        $sqlWhere = implode(' AND ', $where);
        $total = (int) ($this->fetchOne(
            "SELECT COUNT(*) AS c
             FROM bulk_enquiries e
             LEFT JOIN products p ON p.id = e.product_id
             WHERE {$sqlWhere}",
            $params
        )['c'] ?? 0);

        $pages = max(1, (int) ceil($total / $perPage));
        $page = max(1, min($page, $pages));
        $offset = ($page - 1) * $perPage;

        $rows = $this->fetchAll(
            "SELECT e.*, p.name AS product_name, p.unit AS product_unit
             FROM bulk_enquiries e
             LEFT JOIN products p ON p.id = e.product_id
             WHERE {$sqlWhere}
             ORDER BY e.created_at DESC
             LIMIT {$perPage} OFFSET {$offset}",
            $params
        );

        return compact('rows', 'total', 'page', 'pages') + ['per_page' => $perPage];
    }

    public function find(int $id): ?array
    {
        return $this->fetchOne(
            "SELECT e.*, p.name AS product_name, p.unit AS product_unit, p.price AS product_price,
                    c.business_name AS customer_business_name, c.owner_name AS customer_owner_name
             FROM bulk_enquiries e
             LEFT JOIN products p ON p.id = e.product_id
             LEFT JOIN customers c ON c.id = e.customer_id
             WHERE e.id = ?",
            [$id]
        );
    }

    /**
     * @param array<string,mixed> $data
     */
    public function create(array $data): int
    {
        $this->execute(
            'INSERT INTO bulk_enquiries
                (customer_id, name, business_name, mobile, product_id, required_quantity,
                 delivery_location, pincode, preferred_delivery_date, additional_requirement, status)
             VALUES (?,?,?,?,?,?,?,?,?,?,\'new\')',
            [
                $data['customer_id'] ?? null,
                $data['name'],
                $data['business_name'] ?? null,
                $data['mobile'],
                $data['product_id'] ?? null,
                $data['required_quantity'],
                $data['delivery_location'],
                $data['pincode'],
                $data['preferred_delivery_date'] ?? null,
                $data['additional_requirement'] ?? null,
            ]
        );

        return (int) $this->db->lastInsertId();
    }

    public function setStatus(int $id, string $status): bool
    {
        return $this->execute(
            'UPDATE bulk_enquiries SET status = ? WHERE id = ?',
            [$status, $id]
        );
    }

    public function updateAdminNotes(int $id, ?string $notes): bool
    {
        return $this->execute(
            'UPDATE bulk_enquiries SET admin_notes = ? WHERE id = ?',
            [$notes, $id]
        );
    }
}
