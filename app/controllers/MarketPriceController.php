<?php

class MarketPriceController extends Controller
{
    public function index(): void
    {
        $this->view('market_prices/index', [
            'title'    => 'Market Prices',
            'rows'     => (new MarketPrice())->listWithToday(),
            'success'  => flash('success'),
            'error'    => flash('error'),
        ]);
    }

    public function save(): void
    {
        $prices = $_POST['price'] ?? [];
        if (!is_array($prices) || $prices === []) {
            flash('error', 'No prices submitted.');
            redirect('market-prices');
        }
        $adminId = (int) auth_user()['id'];
        $model = new MarketPrice();
        $saved = 0;
        try {
            foreach ($prices as $productId => $price) {
                $productId = (int) $productId;
                $price = trim((string) $price);
                if ($productId <= 0 || $price === '') {
                    continue;
                }
                $val = (float) $price;
                if ($val < 0) {
                    throw new InvalidArgumentException('Prices cannot be negative.');
                }
                $model->upsertToday($productId, $val, $adminId);
                $saved++;
            }
            flash('success', "Saved {$saved} market price(s) for today.");
        } catch (Throwable $e) {
            flash('error', $e->getMessage());
        }
        redirect('market-prices');
    }
}
