<?php

/**
 * Lightweight CSV / XLSX spreadsheet reader (no Composer deps).
 */
class SpreadsheetReader
{
    /**
     * @return array{headers: string[], rows: array<int, array<string, string>>}
     */
    public static function read(string $path): array
    {
        $ext = strtolower(pathinfo($path, PATHINFO_EXTENSION));
        if ($ext === 'csv' || $ext === 'txt') {
            return self::readCsv($path);
        }
        if ($ext === 'xlsx') {
            return self::readXlsx($path);
        }
        throw new RuntimeException('Unsupported file type. Upload a .csv or .xlsx file.');
    }

    /** @return array{headers: string[], rows: array<int, array<string, string>>} */
    public static function readCsv(string $path): array
    {
        $fh = fopen($path, 'rb');
        if ($fh === false) {
            throw new RuntimeException('Unable to open CSV file.');
        }

        // Strip UTF-8 BOM
        $bom = fread($fh, 3);
        if ($bom !== "\xEF\xBB\xBF") {
            rewind($fh);
        }

        $headers = null;
        $rows = [];
        while (($data = fgetcsv($fh)) !== false) {
            if ($data === [null] || $data === false) {
                continue;
            }
            $data = array_map(static fn($v) => trim((string) $v), $data);
            if ($headers === null) {
                $headers = array_map(static fn($h) => self::normalizeHeader($h), $data);
                continue;
            }
            if (self::rowEmpty($data)) {
                continue;
            }
            $assoc = [];
            foreach ($headers as $i => $key) {
                if ($key === '') {
                    continue;
                }
                $assoc[$key] = $data[$i] ?? '';
            }
            $rows[] = $assoc;
        }
        fclose($fh);

        if ($headers === null) {
            throw new RuntimeException('CSV file is empty.');
        }

        return ['headers' => $headers, 'rows' => $rows];
    }

    /** Minimal XLSX first-sheet reader via ZipArchive. */
    public static function readXlsx(string $path): array
    {
        if (!class_exists('ZipArchive')) {
            throw new RuntimeException('ZipArchive extension required for .xlsx files. Use CSV instead.');
        }
        $zip = new ZipArchive();
        if ($zip->open($path) !== true) {
            throw new RuntimeException('Unable to open XLSX file.');
        }

        $shared = [];
        $ss = $zip->getFromName('xl/sharedStrings.xml');
        if ($ss !== false) {
            $xml = @simplexml_load_string($ss);
            if ($xml) {
                foreach ($xml->si as $si) {
                    if (isset($si->t)) {
                        $shared[] = (string) $si->t;
                    } else {
                        $text = '';
                        foreach ($si->r as $r) {
                            $text .= (string) $r->t;
                        }
                        $shared[] = $text;
                    }
                }
            }
        }

        $sheetName = 'xl/worksheets/sheet1.xml';
        $sheet = $zip->getFromName($sheetName);
        if ($sheet === false) {
            $zip->close();
            throw new RuntimeException('XLSX has no sheet1.');
        }
        $zip->close();

        $xml = @simplexml_load_string($sheet);
        if (!$xml) {
            throw new RuntimeException('Invalid XLSX sheet XML.');
        }

        $matrix = [];
        foreach ($xml->sheetData->row as $row) {
            $rIdx = ((int) $row['r']) - 1;
            foreach ($row->c as $c) {
                $ref = (string) $c['r'];
                $col = self::colIndex(preg_replace('/\d+/', '', $ref) ?? 'A');
                $type = (string) ($c['t'] ?? '');
                $val = '';
                if ($type === 's') {
                    $idx = (int) $c->v;
                    $val = $shared[$idx] ?? '';
                } elseif ($type === 'inlineStr') {
                    $val = (string) ($c->is->t ?? '');
                } else {
                    $val = (string) ($c->v ?? '');
                }
                $matrix[$rIdx][$col] = trim($val);
            }
        }

        if ($matrix === []) {
            throw new RuntimeException('XLSX sheet is empty.');
        }
        ksort($matrix);
        $first = array_shift($matrix);
        $maxCol = $first ? max(array_keys($first)) : 0;
        $headers = [];
        for ($i = 0; $i <= $maxCol; $i++) {
            $headers[$i] = self::normalizeHeader($first[$i] ?? '');
        }

        $rows = [];
        foreach ($matrix as $row) {
            $assoc = [];
            $vals = [];
            foreach ($headers as $i => $key) {
                if ($key === '') {
                    continue;
                }
                $v = $row[$i] ?? '';
                $assoc[$key] = $v;
                $vals[] = $v;
            }
            if (!self::rowEmpty($vals)) {
                $rows[] = $assoc;
            }
        }

        return ['headers' => array_values(array_filter($headers, fn($h) => $h !== '')), 'rows' => $rows];
    }

    public static function normalizeHeader(string $h): string
    {
        $h = strtolower(trim($h));
        $h = str_replace([' ', '-', '.'], '_', $h);
        $aliases = [
            'category_name' => 'category',
            'product_name'  => 'name',
            'qty'           => 'stock',
            'quantity'      => 'stock',
            'new_stock'     => 'stock',
            'sku'           => 'item_code',
            'code'          => 'item_code',
            'product_id'    => 'id',
            'minimum_order_quantity' => 'moq',
        ];
        return $aliases[$h] ?? $h;
    }

    private static function rowEmpty(array $data): bool
    {
        foreach ($data as $v) {
            if (trim((string) $v) !== '') {
                return false;
            }
        }
        return true;
    }

    private static function colIndex(string $letters): int
    {
        $letters = strtoupper($letters);
        $n = 0;
        for ($i = 0; $i < strlen($letters); $i++) {
            $n = $n * 26 + (ord($letters[$i]) - 64);
        }
        return $n - 1;
    }
}
