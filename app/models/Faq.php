<?php

class Faq extends Model
{
    public function search(?string $q = null, ?string $category = null): array
    {
        $where = ['is_active = 1'];
        $params = [];
        if ($q !== null && $q !== '') {
            $where[] = '(question LIKE ? OR answer LIKE ?)';
            $like = '%' . $q . '%';
            array_push($params, $like, $like);
        }
        if ($category !== null && $category !== '') {
            $where[] = 'category = ?';
            $params[] = $category;
        }
        $sqlWhere = implode(' AND ', $where);
        return $this->fetchAll(
            "SELECT id, question, answer, category, sort_order
             FROM faqs WHERE {$sqlWhere}
             ORDER BY sort_order ASC, id ASC",
            $params
        );
    }
}
