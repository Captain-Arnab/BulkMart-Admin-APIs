<?php

class OfferController extends Controller
{
    public function index(): void
    {
        $filters = [
            'q'      => trim((string) ($_GET['q'] ?? '')),
            'active' => trim((string) ($_GET['active'] ?? '')),
        ];
        $banners = (new Banner())->all();
        $offers = (new Offer())->all();
        if ($filters['q'] !== '') {
            $q = mb_strtolower($filters['q']);
            $banners = array_values(array_filter($banners, static fn (array $b): bool =>
                str_contains(mb_strtolower((string) ($b['title'] ?? '')), $q)
                || str_contains(mb_strtolower((string) ($b['description'] ?? '')), $q)
            ));
            $offers = array_values(array_filter($offers, static function (array $o) use ($q): bool {
                return str_contains(mb_strtolower((string) $o['title']), $q)
                    || str_contains(mb_strtolower((string) ($o['coupon_code'] ?? '')), $q);
            }));
        }
        if ($filters['active'] === '1' || $filters['active'] === '0') {
            $want = (int) $filters['active'];
            $banners = array_values(array_filter($banners, static fn (array $b): bool => (int) $b['is_active'] === $want));
            $offers = array_values(array_filter($offers, static fn (array $o): bool => (int) $o['is_active'] === $want));
        }
        $this->view('offers/index', [
            'title'   => 'Offers & Banners',
            'banners' => $banners,
            'offers'  => $offers,
            'filters' => $filters,
            'success' => flash('success'),
            'error'   => flash('error'),
        ]);
    }

    public function createBanner(): void
    {
        $this->view('offers/banner_form', [
            'title'  => 'Add Banner',
            'banner' => null,
            'error'  => flash('error'),
        ]);
    }

    public function storeBanner(): void
    {
        try {
            $data = $this->bannerPayload();
            if (!empty($_FILES['image']['name'])) {
                $data['image_url'] = UploadService::storeImage($_FILES['image'], 'banners');
            } else {
                $data['image_url'] = null;
            }
            (new Banner())->create($data);
            flash('success', 'Banner created.');
            redirect('offers');
        } catch (Throwable $e) {
            flash('error', $e->getMessage());
            redirect('offers/banners/create');
        }
    }

    public function editBanner(string $id): void
    {
        $banner = (new Banner())->find((int) $id);
        if (!$banner) {
            flash('error', 'Banner not found.');
            redirect('offers');
        }
        $this->view('offers/banner_form', [
            'title'  => 'Edit Banner',
            'banner' => $banner,
            'error'  => flash('error'),
        ]);
    }

    public function updateBanner(string $id): void
    {
        $model = new Banner();
        $banner = $model->find((int) $id);
        if (!$banner) {
            flash('error', 'Banner not found.');
            redirect('offers');
        }
        try {
            $data = $this->bannerPayload();
            $data['image_url'] = $banner['image_url'];
            if (!empty($_FILES['image']['name'])) {
                $data['image_url'] = UploadService::storeImage($_FILES['image'], 'banners');
            }
            $model->update((int) $id, $data);
            flash('success', 'Banner updated.');
            redirect('offers');
        } catch (Throwable $e) {
            flash('error', $e->getMessage());
            redirect('offers/banners/' . (int) $id . '/edit');
        }
    }

    public function deleteBanner(string $id): void
    {
        (new Banner())->delete((int) $id);
        flash('success', 'Banner deleted.');
        redirect('offers');
    }

    public function createOffer(): void
    {
        $this->view('offers/offer_form', [
            'title'      => 'Add Offer',
            'offer'      => null,
            'categories' => (new Category())->options(),
            'error'      => flash('error'),
        ]);
    }

    public function storeOffer(): void
    {
        try {
            (new Offer())->create($this->offerPayload());
            flash('success', 'Offer created.');
            redirect('offers');
        } catch (Throwable $e) {
            flash('error', $e->getMessage());
            redirect('offers/create');
        }
    }

    public function editOffer(string $id): void
    {
        $offer = (new Offer())->find((int) $id);
        if (!$offer) {
            flash('error', 'Offer not found.');
            redirect('offers');
        }
        $this->view('offers/offer_form', [
            'title'      => 'Edit Offer',
            'offer'      => $offer,
            'categories' => (new Category())->options(),
            'error'      => flash('error'),
        ]);
    }

    public function updateOffer(string $id): void
    {
        $model = new Offer();
        if (!$model->find((int) $id)) {
            flash('error', 'Offer not found.');
            redirect('offers');
        }
        try {
            $model->update((int) $id, $this->offerPayload());
            flash('success', 'Offer updated.');
            redirect('offers');
        } catch (Throwable $e) {
            flash('error', $e->getMessage());
            redirect('offers/' . (int) $id . '/edit');
        }
    }

    public function deleteOffer(string $id): void
    {
        (new Offer())->delete((int) $id);
        flash('success', 'Offer deleted.');
        redirect('offers');
    }

    private function bannerPayload(): array
    {
        $title = trim((string) ($_POST['title'] ?? ''));
        $description = trim((string) ($_POST['description'] ?? ''));
        $link = trim((string) ($_POST['link'] ?? ''));
        if (vc_strlen($title) > VC_BANNER_TITLE_MAX) {
            throw new InvalidArgumentException('Title can be at most ' . VC_BANNER_TITLE_MAX . ' characters.');
        }
        if (vc_strlen($description) > VC_BANNER_DESC_MAX) {
            throw new InvalidArgumentException('Description can be at most ' . VC_BANNER_DESC_MAX . ' characters.');
        }
        if (vc_strlen($link) > VC_BANNER_LINK_MAX) {
            throw new InvalidArgumentException('Link can be at most ' . VC_BANNER_LINK_MAX . ' characters.');
        }
        return [
            'title'       => $title !== '' ? $title : null,
            'description' => $description !== '' ? $description : null,
            'link'        => $link !== '' ? $link : null,
            'active_from' => $this->normalizeDateTime($_POST['active_from'] ?? ''),
            'active_to'   => $this->normalizeDateTime($_POST['active_to'] ?? ''),
            'sort_order'  => (int) ($_POST['sort_order'] ?? 0),
            'is_active'   => isset($_POST['is_active']) ? 1 : 0,
            'image_url'   => null,
        ];
    }

    private function offerPayload(): array
    {
        $title = trim((string) ($_POST['title'] ?? ''));
        $type = trim((string) ($_POST['discount_type'] ?? ''));
        $value = (float) ($_POST['discount_value'] ?? 0);
        if ($title === '' || !in_array($type, ['percentage', 'flat'], true) || $value <= 0) {
            throw new InvalidArgumentException('Title, discount type, and a positive discount value are required.');
        }
        return [
            'title'         => $title,
            'discount_type' => $type,
            'discount_value'=> $value,
            'min_qty'       => ($_POST['min_qty'] ?? '') === '' ? null : (float) $_POST['min_qty'],
            'category_id'   => (int) ($_POST['category_id'] ?? 0) ?: null,
            'coupon_code'   => trim((string) ($_POST['coupon_code'] ?? '')) ?: null,
            'valid_from'    => $this->normalizeDateTime($_POST['valid_from'] ?? ''),
            'valid_till'    => $this->normalizeDateTime($_POST['valid_till'] ?? ''),
            'is_active'     => isset($_POST['is_active']) ? 1 : 0,
        ];
    }

    private function normalizeDateTime($value): ?string
    {
        $value = trim((string) $value);
        if ($value === '') {
            return null;
        }
        return str_replace('T', ' ', $value) . (strlen($value) === 16 ? ':00' : '');
    }
}
