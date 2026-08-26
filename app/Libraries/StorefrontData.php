<?php

namespace App\Libraries;

use App\Models\CategoryModel;
use App\Models\DeliveryZoneModel;
use App\Models\GalleryModel;
use App\Models\OrderItemModel;
use App\Models\OrderModel;
use App\Models\ProductModel;
use App\Models\TestimonialModel;

/**
 * Assembles the data the storefront's client bundle used to hold as hardcoded
 * JS constants (RAW/PRODUCTS/ZONES/TESTIMONIALS/...).
 *
 * The shapes here are deliberately identical to what the client already
 * consumes, so `_logic.php` only had to swap literal definitions for injected
 * JSON — none of the downstream rendering logic changed.
 */
class StorefrontData
{
    /** "Best Sellers" is a computed view, not a stored category. */
    public const VIRTUAL_CATEGORY = 'Best Sellers';

    public function payload(): array
    {
        $categories = (new CategoryModel())->ordered();
        $products   = $this->products($categories);

        return [
            'CATS'          => $this->catNames($categories),
            'CAT_META'      => $this->catMeta($categories),
            'PRODUCTS'      => $products,
            'FEATURED'      => $this->featured($products),
            'ITEM_MOQ'      => $this->itemMoq($products),
            'ZONES'         => $this->zones(),
            'COD_ZONES'     => $this->codZones(),
            'TESTIMONIALS'  => $this->testimonials(),
            'GALLERY'       => $this->gallery(),
            'ORDERS'        => $this->orders(),
        ];
    }

    private function catNames(array $categories): array
    {
        return array_merge([self::VIRTUAL_CATEGORY], array_column($categories, 'name'));
    }

    private function catMeta(array $categories): array
    {
        $meta = [];
        foreach ($categories as $c) {
            $meta[$c['name']] = [
                'image' => $c['image'] ?? '',
                'blurb' => $c['blurb'] ?? '',
            ];
        }

        return $meta;
    }

    private function products(array $categories): array
    {
        $catById = array_column($categories, 'name', 'id');
        $rows    = (new ProductModel())
            ->where('is_active', 1)
            ->orderBy('category_id', 'ASC')->orderBy('id', 'ASC')
            ->findAll();

        $out = [];
        foreach ($rows as $r) {
            $out[] = [
                'id'    => (int) $r['id'],
                'slug'  => $r['slug'],
                'name'  => $r['name'],
                'note'  => $r['note'] ?? '',
                'price' => (int) $r['price'],
                'isNew' => (bool) $r['is_new'],
                'cat'   => $catById[(int) $r['category_id']] ?? '',
                'image' => $r['image'] ?? '',
                'ing'   => $r['ingredients'] ?? '',
                'kcal'  => (int) ($r['kcal'] ?? 0),
            ];
        }

        return $out;
    }

    /**
     * Home-page picks: explicitly flagged products first, topped up with the
     * genuine best sellers from order history.
     */
    private function featured(array $products): array
    {
        $flagged = array_column(
            (new ProductModel())->where('is_featured', 1)->where('is_active', 1)->findAll(),
            'name'
        );

        $byName = array_column($products, 'name', 'name');
        $picks  = array_values(array_intersect($flagged, $byName));

        foreach (array_keys((new OrderModel())->unitsSold()) as $name) {
            if (count($picks) >= 4) {
                break;
            }
            if (isset($byName[$name]) && !in_array($name, $picks, true)) {
                $picks[] = $name;
            }
        }

        return array_slice($picks, 0, 4);
    }

    private function itemMoq(array $products): array
    {
        $moq  = [];
        $rows = (new ProductModel())->where('is_active', 1)->where('min_qty >', 1)->findAll();
        foreach ($rows as $r) {
            $moq[$r['name']] = (int) $r['min_qty'];
        }

        return $moq;
    }

    private function zones(): array
    {
        $out = [];
        foreach ((new DeliveryZoneModel())->ordered() as $z) {
            $row = [
                'name' => $z['name'],
                // null fee means the area is not served — distinct from free.
                'fee'  => $z['fee'] === null ? null : (int) $z['fee'],
            ];
            if ($z['is_limited']) {
                $row['limited'] = true;
            }
            $out[] = $row;
        }

        return $out;
    }

    private function codZones(): array
    {
        return array_column(
            (new DeliveryZoneModel())->where('cod_allowed', 1)->orderBy('name')->findAll(),
            'name'
        );
    }

    private function testimonials(): array
    {
        $out = [];
        foreach ((new TestimonialModel())->where('is_published', 1)->orderBy('id', 'ASC')->findAll() as $t) {
            $out[] = [
                'name'  => $t['name'],
                'stars' => str_repeat('★', max(1, min(5, (int) $t['stars']))),
                'quote' => $t['quote'],
                'item'  => $t['item'] ?? '',
            ];
        }

        return $out;
    }

    private function gallery(): array
    {
        $out = [];
        foreach ((new GalleryModel())->ordered() as $g) {
            $out[] = [
                'src'  => $g['src'],
                'alt'  => $g['alt'] ?? '',
                'span' => (int) $g['span'],
            ];
        }

        return $out;
    }

    /**
     * Order history for the account area. Scoped to one customer when signed
     * in; an empty list otherwise, so one shopper never sees another's orders.
     */
    private function orders(): array
    {
        $customerId = session()->get('customerId');
        if (!$customerId) {
            return [];
        }

        $orders = (new OrderModel())
            ->where('customer_id', (int) $customerId)
            ->orderBy('placed_on', 'DESC')->orderBy('id', 'DESC')
            ->findAll();

        if (!$orders) {
            return [];
        }

        $itemModel = new OrderItemModel();
        $out       = [];

        foreach ($orders as $o) {
            $items = [];
            foreach ($itemModel->forOrder((int) $o['id']) as $i) {
                $items[] = [$i['product_name'], (int) $i['qty'], (int) $i['unit_price']];
            }

            $out[] = [
                'no'       => '#' . ltrim($o['order_no'], '#'),
                'date'     => $o['placed_on'] ? date('F j, Y', strtotime($o['placed_on'])) : '',
                'payment'  => $o['payment_method'] ?? '',
                'status'   => $o['status'],
                'discount' => (int) $o['discount'],
                'items'    => $items,
                'address'  => $o['address'] ?? '',
                'notes'    => $o['notes'] ?? '',
            ];
        }

        return $out;
    }
}
