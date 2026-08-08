<?php

class OfferController extends Controller
{
    public function index(): void
    {
        $this->view('shared/placeholder', [
            'title'       => 'Offers & Banners',
            'module'      => 'offers',
            'moduleLabel' => 'Offers & Banners',
            'breadcrumb'  => [
                ['label' => 'Home', 'url' => url('dashboard')],
                ['label' => 'Offers & Banners', 'url' => null],
            ],
        ]);
    }
}
