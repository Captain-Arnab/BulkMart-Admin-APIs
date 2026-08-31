<?php

/**
 * Minimal PDF writer for invoices (no external dependencies).
 * Uses Helvetica / WinAnsi — currency amounts use "Rs.".
 */
class InvoicePdfService
{
    /** @var array<int,string> */
    private array $objects = [];
    private int $objectCount = 0;
    /** @var list<int> */
    private array $pageIds = [];
    private string $content = '';
    private float $pageWidth = 595.28;
    private float $pageHeight = 841.89;
    private float $y = 0.0;
    private float $margin = 48.0;
    private float $fontSize = 11.0;
    private string $currentFont = 'F1';

    /** @param array<string,mixed> $invoice */
    public function build(array $invoice): string
    {
        $this->objects = [];
        $this->objectCount = 0;
        $this->pageIds = [];
        $this->startPage();

        $company = (string) ($invoice['company']['name'] ?? 'VeggiiCart');
        $invNo = (string) ($invoice['invoice_number'] ?? '');
        $orderNo = (string) ($invoice['order_number'] ?? '');
        $placed = (string) ($invoice['placed_at'] ?? '');
        $status = (string) ($invoice['status_label'] ?? ($invoice['status'] ?? ''));
        $payment = strtoupper((string) ($invoice['payment_method'] ?? 'COD'));

        $this->setFont(18, true);
        $this->writeText($this->margin, $this->y, $company);
        $this->y -= 22;
        $this->setFont(11, false);
        $this->writeText($this->margin, $this->y, 'Tax Invoice / Order Invoice');
        $this->y -= 18;
        $this->setFont(10, false);
        $this->writeText($this->margin, $this->y, 'Invoice: ' . $invNo);
        $this->y -= 14;
        $this->writeText($this->margin, $this->y, 'Order: ' . $orderNo . '  |  Date: ' . $placed);
        $this->y -= 14;
        $this->writeText($this->margin, $this->y, 'Status: ' . $status . '  |  Payment: ' . $payment);
        $this->y -= 22;

        $cust = $invoice['customer'] ?? [];
        $addr = $invoice['billing_address'] ?? [];
        $this->setFont(11, true);
        $this->writeText($this->margin, $this->y, 'Bill To');
        $this->y -= 15;
        $this->setFont(10, false);
        foreach ([
            (string) ($cust['business_name'] ?? ''),
            trim(((string) ($cust['owner_name'] ?? '')) . '  ' . ((string) ($cust['mobile'] ?? ''))),
            (string) ($cust['gst_number'] ?? '') !== '' ? 'GSTIN: ' . (string) $cust['gst_number'] : '',
            trim(implode(', ', array_filter([
                (string) ($addr['line1'] ?? ''),
                (string) ($addr['line2'] ?? ''),
                (string) ($addr['landmark'] ?? ''),
                (string) ($addr['city'] ?? ''),
                (string) ($addr['state'] ?? ''),
                (string) ($addr['pincode'] ?? ''),
            ]))),
        ] as $line) {
            $line = trim((string) $line);
            if ($line === '') {
                continue;
            }
            $this->writeText($this->margin, $this->y, $line);
            $this->y -= 13;
        }

        $this->y -= 10;
        $this->drawLine($this->margin, $this->y, $this->pageWidth - $this->margin, $this->y);
        $this->y -= 18;

        $cols = [
            ['label' => 'Item', 'x' => $this->margin],
            ['label' => 'Unit', 'x' => $this->margin + 220],
            ['label' => 'Qty', 'x' => $this->margin + 275],
            ['label' => 'Price', 'x' => $this->margin + 325],
            ['label' => 'Total', 'x' => $this->margin + 405],
        ];
        $this->setFont(10, true);
        foreach ($cols as $col) {
            $this->writeText($col['x'], $this->y, $col['label']);
        }
        $this->y -= 8;
        $this->drawLine($this->margin, $this->y, $this->pageWidth - $this->margin, $this->y);
        $this->y -= 16;
        $this->setFont(9, false);

        foreach (($invoice['items'] ?? []) as $item) {
            if ($this->y < 100) {
                $this->endPage();
                $this->startPage();
                $this->setFont(9, false);
            }
            $name = $this->clip((string) ($item['name'] ?? ''), 42);
            $unit = $this->clip((string) ($item['unit'] ?? ''), 10);
            $qty = number_format((float) ($item['quantity'] ?? 0), 2);
            $price = 'Rs.' . number_format((float) ($item['unit_price'] ?? 0), 2);
            $total = 'Rs.' . number_format((float) ($item['line_total'] ?? 0), 2);
            $this->writeText($cols[0]['x'], $this->y, $name);
            $this->writeText($cols[1]['x'], $this->y, $unit);
            $this->writeText($cols[2]['x'], $this->y, $qty);
            $this->writeText($cols[3]['x'], $this->y, $price);
            $this->writeText($cols[4]['x'], $this->y, $total);
            $this->y -= 14;
        }

        $this->y -= 6;
        $this->drawLine($this->margin, $this->y, $this->pageWidth - $this->margin, $this->y);
        $this->y -= 18;
        $this->setFont(10, false);

        $rightX = $this->pageWidth - $this->margin - 160;
        $summary = [
            ['Subtotal', 'Rs.' . number_format((float) ($invoice['subtotal'] ?? 0), 2)],
            [
                'Discount' . (!empty($invoice['coupon_code']) ? ' (' . $invoice['coupon_code'] . ')' : ''),
                'Rs.' . number_format((float) ($invoice['discount_amount'] ?? 0), 2),
            ],
            ['Delivery', 'Rs.' . number_format((float) ($invoice['delivery_fee'] ?? 0), 2)],
        ];
        foreach ($summary as [$label, $value]) {
            $this->writeText($rightX, $this->y, (string) $label);
            $this->writeText($rightX + 90, $this->y, (string) $value);
            $this->y -= 14;
        }
        $this->setFont(12, true);
        $this->writeText($rightX, $this->y, 'Total');
        $this->writeText($rightX + 90, $this->y, 'Rs.' . number_format((float) ($invoice['total'] ?? 0), 2));
        $this->y -= 28;

        $this->setFont(9, false);
        $phone = (string) ($invoice['company']['phone'] ?? '');
        $email = (string) ($invoice['company']['email'] ?? '');
        $this->writeText($this->margin, $this->y, 'Thank you for ordering with ' . $company . '.');
        $this->y -= 12;
        if ($phone !== '' || $email !== '') {
            $this->writeText(
                $this->margin,
                $this->y,
                trim('Support: ' . $phone . ($phone !== '' && $email !== '' ? ' | ' : '') . $email)
            );
        }

        $this->endPage();
        return $this->finalize();
    }

    private function startPage(): void
    {
        $this->content = "0.2 w\n";
        $this->y = $this->pageHeight - $this->margin;
        $this->setFont(11, false);
    }

    private function endPage(): void
    {
        $streamId = $this->addObject("<< /Length " . strlen($this->content) . " >>\nstream\n" . $this->content . "\nendstream");
        $this->pageIds[] = $this->addObject(
            "<< /Type /Page /Parent PAGES_REF /MediaBox [0 0 {$this->pageWidth} {$this->pageHeight}] "
            . "/Contents {$streamId} 0 R /Resources << /Font << /F1 FONT1_REF /F2 FONT2_REF >> >> >>"
        );
    }

    private function setFont(float $size, bool $bold): void
    {
        $this->fontSize = $size;
        $this->currentFont = $bold ? 'F2' : 'F1';
    }

    private function writeText(float $x, float $y, string $text): void
    {
        $safe = $this->escape($this->toWinAnsi($text));
        $this->content .= sprintf(
            "BT /%s %.2F Tf 1 0 0 1 %.2F %.2F Tm (%s) Tj ET\n",
            $this->currentFont,
            $this->fontSize,
            $x,
            $y,
            $safe
        );
    }

    private function drawLine(float $x1, float $y1, float $x2, float $y2): void
    {
        $this->content .= sprintf("%.2F %.2F m %.2F %.2F l S\n", $x1, $y1, $x2, $y2);
    }

    private function addObject(string $body): int
    {
        $this->objectCount++;
        $this->objects[$this->objectCount] = $body;
        return $this->objectCount;
    }

    private function finalize(): string
    {
        $font1 = $this->addObject('<< /Type /Font /Subtype /Type1 /BaseFont /Helvetica /Encoding /WinAnsiEncoding >>');
        $font2 = $this->addObject('<< /Type /Font /Subtype /Type1 /BaseFont /Helvetica-Bold /Encoding /WinAnsiEncoding >>');

        $kids = implode(' ', array_map(static fn (int $id): string => $id . ' 0 R', $this->pageIds));
        $pagesId = $this->addObject('<< /Type /Pages /Kids [' . $kids . '] /Count ' . count($this->pageIds) . ' >>');

        foreach ($this->pageIds as $pageId) {
            $body = $this->objects[$pageId];
            $body = str_replace('PAGES_REF', $pagesId . ' 0 R', $body);
            $body = str_replace('FONT1_REF', $font1 . ' 0 R', $body);
            $body = str_replace('FONT2_REF', $font2 . ' 0 R', $body);
            $this->objects[$pageId] = $body;
        }

        $catalogId = $this->addObject('<< /Type /Catalog /Pages ' . $pagesId . ' 0 R >>');

        $pdf = "%PDF-1.4\n";
        $offsets = [0];
        for ($i = 1; $i <= $this->objectCount; $i++) {
            $offsets[$i] = strlen($pdf);
            $pdf .= $i . " 0 obj\n" . $this->objects[$i] . "\nendobj\n";
        }
        $xrefPos = strlen($pdf);
        $pdf .= 'xref' . "\n0 " . ($this->objectCount + 1) . "\n";
        $pdf .= "0000000000 65535 f \n";
        for ($i = 1; $i <= $this->objectCount; $i++) {
            $pdf .= sprintf("%010d 00000 n \n", $offsets[$i]);
        }
        $pdf .= "trailer\n<< /Size " . ($this->objectCount + 1) . " /Root {$catalogId} 0 R >>\n";
        $pdf .= "startxref\n{$xrefPos}\n%%EOF";
        return $pdf;
    }

    private function escape(string $text): string
    {
        return str_replace(['\\', '(', ')'], ['\\\\', '\\(', '\\)'], $text);
    }

    private function toWinAnsi(string $text): string
    {
        $text = str_replace(['₹', '—', '–', '’', '‘', '“', '”'], ['Rs.', '-', '-', "'", "'", '"', '"'], $text);
        $converted = @iconv('UTF-8', 'Windows-1252//TRANSLIT//IGNORE', $text);
        if ($converted === false) {
            return preg_replace('/[^\x20-\x7E]/', '?', $text) ?? $text;
        }
        return $converted;
    }

    private function clip(string $text, int $max): string
    {
        if (strlen($text) <= $max) {
            return $text;
        }
        return substr($text, 0, max(0, $max - 3)) . '...';
    }
}
