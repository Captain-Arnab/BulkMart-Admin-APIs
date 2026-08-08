<?php

class MarketPriceController extends Controller
{
    public function index(): void
    {
        $this->view('shared/placeholder', [
            'title'       => 'Market Prices',
            'module'      => 'market_prices',
            'moduleLabel' => 'Market Prices',
            'breadcrumb'  => [
                ['label' => 'Home', 'url' => url('dashboard')],
                ['label' => 'Market Prices', 'url' => null],
            ],
        ]);
    }
}
