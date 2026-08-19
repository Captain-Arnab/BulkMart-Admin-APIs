<?php

class Customer extends Model
{
    public const KYC_BADGE = [
        'pending'  => 'bg-warning text-dark',
        'approved' => 'bg-success',
        'rejected' => 'bg-danger',
    ];

    public const DOC_LABELS = [
        'gst_certificate'    => 'GST Certificate',
        'fssai_license'      => 'FSSAI Licence',
        'shop_establishment' => 'Shop Registration',
        'msme_certificate'   => 'MSME Certificate',
        'trade_license'      => 'Trade Licence',
        'pan_card'           => 'PAN Card',
        'aadhaar_card'       => 'Aadhaar Card',
        'business_photo'     => 'Shop-front Photo',
        'owner_photo'        => 'Business Visiting Card',
        'cancelled_cheque'   => 'Cancelled Cheque',
    ];

    public const DOC_ALIASES = [
        'fssai_document'     => 'fssai_license',
        'shop_registration'  => 'shop_establishment',
        'trade_licence'      => 'trade_license',
        'shop_photo'         => 'business_photo',
        'business_card'      => 'owner_photo',
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

    public function findByMobile(string $mobile): ?array
    {
        return $this->fetchOne('SELECT * FROM customers WHERE mobile = ?', [$mobile]);
    }

    public function findByEmail(string $email): ?array
    {
        return $this->fetchOne('SELECT * FROM customers WHERE email = ?', [$email]);
    }

    /** Create a stub customer after first OTP verify (registration completes later). */
    public function createFromMobile(string $mobile): int
    {
        $this->execute(
            "INSERT INTO customers
              (mobile, business_name, owner_name, business_type, kyc_status)
             VALUES (?,?,?,?, 'pending')",
            [$mobile, 'Pending registration', 'Pending', 'unregistered']
        );
        return (int) $this->db->lastInsertId();
    }

    public function updateProfile(int $id, array $d): bool
    {
        $fields = [];
        $params = [];
        foreach (['email', 'owner_name', 'business_name', 'avatar_url', 'gst_number', 'fssai_number', 'pan_number'] as $col) {
            if (array_key_exists($col, $d)) {
                $fields[] = "`$col` = ?";
                $params[] = $d[$col];
            }
        }
        if ($fields === []) {
            return true;
        }
        $params[] = $id;
        return $this->execute('UPDATE customers SET ' . implode(', ', $fields) . ' WHERE id = ?', $params);
    }

    public function submitRegistration(int $id, array $d): bool
    {
        return $this->execute(
            "UPDATE customers SET
                business_name = ?, owner_name = ?, business_type = ?,
                gst_number = ?, fssai_number = ?, pan_number = ?,
                email = COALESCE(?, email),
                kyc_status = 'pending', kyc_rejection_reason = NULL
             WHERE id = ?",
            [
                $d['business_name'],
                $d['owner_name'],
                $d['business_type'],
                $d['gst_number'] ?? null,
                $d['fssai_number'] ?? null,
                $d['pan_number'] ?? null,
                $d['email'] ?? null,
                $id,
            ]
        );
    }

    public function addDocument(int $customerId, string $type, string $fileUrl): int
    {
        $this->execute(
            'INSERT INTO customer_documents (customer_id, document_type, file_url) VALUES (?,?,?)',
            [$customerId, $type, $fileUrl]
        );
        return (int) $this->db->lastInsertId();
    }

    /** After rejection — reset to pending for re-review. */
    public function resubmitKyc(int $id): bool
    {
        return $this->execute(
            "UPDATE customers SET kyc_status = 'pending', kyc_rejection_reason = NULL WHERE id = ? AND kyc_status = 'rejected'",
            [$id]
        );
    }

    public function clearAvatar(int $id): bool
    {
        return $this->execute('UPDATE customers SET avatar_url = NULL WHERE id = ?', [$id]);
    }

    public function publicProfile(array $row): array
    {
        return [
            'id'                   => (int) $row['id'],
            'mobile'               => $row['mobile'],
            'email'                => $row['email'],
            'business_name'        => $row['business_name'],
            'owner_name'           => $row['owner_name'],
            'business_type'        => $row['business_type'],
            'gst_number'           => $row['gst_number'],
            'fssai_number'         => $row['fssai_number'] ?? null,
            'pan_number'           => $row['pan_number'] ?? null,
            'avatar_url'           => $row['avatar_url'] ? media($row['avatar_url']) : null,
            'kyc_status'           => $row['kyc_status'],
            'kyc_rejection_reason' => $row['kyc_rejection_reason'],
            'is_blocked'           => (int) ($row['is_blocked'] ?? 0) === 1,
            'registration_complete'=> !in_array(($row['business_type'] ?? ''), ['unregistered', ''], true)
                && ($row['business_name'] ?? '') !== 'Pending registration',
        ];
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
