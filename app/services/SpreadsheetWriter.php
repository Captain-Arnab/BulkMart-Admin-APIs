<?php

/**
 * Lightweight XLSX writer (no Composer deps) via ZipArchive + OOXML.
 */
class SpreadsheetWriter
{
    /**
     * Stream an .xlsx download and exit.
     *
     * @param string               $filename Download filename (with or without .xlsx)
     * @param array<int, string>   $headers  Column titles
     * @param array<int, array>    $rows     List of row arrays (assoc keyed by header or numeric)
     */
    public static function downloadXlsx(string $filename, array $headers, array $rows): void
    {
        if (!class_exists('ZipArchive')) {
            throw new RuntimeException('ZipArchive extension is required for Excel export.');
        }

        $filename = trim($filename);
        if ($filename === '') {
            $filename = 'export.xlsx';
        }
        if (!str_ends_with(strtolower($filename), '.xlsx')) {
            $filename .= '.xlsx';
        }

        $binary = self::buildXlsx($headers, $rows);

        header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
        header('Content-Disposition: attachment; filename="' . str_replace('"', '', $filename) . '"');
        header('Content-Length: ' . strlen($binary));
        header('Cache-Control: max-age=0');
        echo $binary;
        exit;
    }

    /**
     * @param array<int, string> $headers
     * @param array<int, array>  $rows
     */
    public static function buildXlsx(array $headers, array $rows): string
    {
        $sheetRows = [];
        $sheetRows[] = array_values($headers);
        foreach ($rows as $row) {
            $line = [];
            foreach ($headers as $i => $title) {
                if (array_key_exists($title, $row)) {
                    $line[] = $row[$title];
                } elseif (array_key_exists($i, $row)) {
                    $line[] = $row[$i];
                } else {
                    $key = self::slug($title);
                    $line[] = $row[$key] ?? '';
                }
            }
            $sheetRows[] = $line;
        }

        $sheetXml = self::sheetXml($sheetRows);
        $tmp = tempnam(sys_get_temp_dir(), 'vcx');
        if ($tmp === false) {
            throw new RuntimeException('Unable to create temp file for Excel export.');
        }
        $zipPath = $tmp . '.xlsx';
        @unlink($tmp);

        $zip = new ZipArchive();
        if ($zip->open($zipPath, ZipArchive::CREATE | ZipArchive::OVERWRITE) !== true) {
            throw new RuntimeException('Unable to create Excel file.');
        }

        $zip->addFromString('[Content_Types].xml', self::contentTypesXml());
        $zip->addFromString('_rels/.rels', self::relsXml());
        $zip->addFromString('xl/workbook.xml', self::workbookXml());
        $zip->addFromString('xl/_rels/workbook.xml.rels', self::workbookRelsXml());
        $zip->addFromString('xl/worksheets/sheet1.xml', $sheetXml);
        $zip->close();

        $binary = file_get_contents($zipPath);
        @unlink($zipPath);
        if ($binary === false) {
            throw new RuntimeException('Unable to read Excel file.');
        }
        return $binary;
    }

    /** @param array<int, array<int, mixed>> $rows */
    private static function sheetXml(array $rows): string
    {
        $xml = '<?xml version="1.0" encoding="UTF-8" standalone="yes"?>'
            . '<worksheet xmlns="http://schemas.openxmlformats.org/spreadsheetml/2006/main">'
            . '<sheetData>';

        foreach ($rows as $rIdx => $cols) {
            $rowNum = $rIdx + 1;
            $xml .= '<row r="' . $rowNum . '">';
            foreach ($cols as $cIdx => $value) {
                $ref = self::colLetter($cIdx) . $rowNum;
                $text = self::xmlEscape((string) $value);
                $xml .= '<c r="' . $ref . '" t="inlineStr"><is><t>' . $text . '</t></is></c>';
            }
            $xml .= '</row>';
        }

        $xml .= '</sheetData></worksheet>';
        return $xml;
    }

    private static function colLetter(int $index): string
    {
        $index = max(0, $index);
        $letters = '';
        while ($index >= 0) {
            $letters = chr(($index % 26) + 65) . $letters;
            $index = intdiv($index, 26) - 1;
        }
        return $letters;
    }

    private static function xmlEscape(string $value): string
    {
        return htmlspecialchars($value, ENT_XML1 | ENT_QUOTES, 'UTF-8');
    }

    private static function slug(string $title): string
    {
        $h = strtolower(trim($title));
        return str_replace([' ', '-', '.'], '_', $h);
    }

    private static function contentTypesXml(): string
    {
        return '<?xml version="1.0" encoding="UTF-8" standalone="yes"?>'
            . '<Types xmlns="http://schemas.openxmlformats.org/package/2006/content-types">'
            . '<Default Extension="rels" ContentType="application/vnd.openxmlformats-package.relationships+xml"/>'
            . '<Default Extension="xml" ContentType="application/xml"/>'
            . '<Override PartName="/xl/workbook.xml" ContentType="application/vnd.openxmlformats-officedocument.spreadsheetml.sheet.main+xml"/>'
            . '<Override PartName="/xl/worksheets/sheet1.xml" ContentType="application/vnd.openxmlformats-officedocument.spreadsheetml.worksheet+xml"/>'
            . '</Types>';
    }

    private static function relsXml(): string
    {
        return '<?xml version="1.0" encoding="UTF-8" standalone="yes"?>'
            . '<Relationships xmlns="http://schemas.openxmlformats.org/package/2006/relationships">'
            . '<Relationship Id="rId1" Type="http://schemas.openxmlformats.org/officeDocument/2006/relationships/officeDocument" Target="xl/workbook.xml"/>'
            . '</Relationships>';
    }

    private static function workbookXml(): string
    {
        return '<?xml version="1.0" encoding="UTF-8" standalone="yes"?>'
            . '<workbook xmlns="http://schemas.openxmlformats.org/spreadsheetml/2006/main" '
            . 'xmlns:r="http://schemas.openxmlformats.org/officeDocument/2006/relationships">'
            . '<sheets><sheet name="Sheet1" sheetId="1" r:id="rId1"/></sheets>'
            . '</workbook>';
    }

    private static function workbookRelsXml(): string
    {
        return '<?xml version="1.0" encoding="UTF-8" standalone="yes"?>'
            . '<Relationships xmlns="http://schemas.openxmlformats.org/package/2006/relationships">'
            . '<Relationship Id="rId1" Type="http://schemas.openxmlformats.org/officeDocument/2006/relationships/worksheet" Target="worksheets/sheet1.xml"/>'
            . '</Relationships>';
    }
}
