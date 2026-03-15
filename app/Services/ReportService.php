<?php

namespace App\Services;

use App\Services\BaseService;
use App\Models\Order;
use App\Models\OrderItem;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

class ReportService extends BaseService
{
    public function generateDailyReport(?Carbon $date = null): array
    {
        $date  = $date ?? Carbon::yesterday();
        $start = $date->copy()->startOfDay();
        $end   = $date->copy()->endOfDay();

        return [
            'date'                    => $date->format('d/m/Y'),
            'most_sold_product'       => $this->getMostSoldProduct($start, $end),
            'least_sold_product'      => $this->getLeastSoldProduct($start, $end),
            'highest_revenue_product' => $this->getHighestRevenueProduct($start, $end),
            'lowest_revenue_product'  => $this->getLowestRevenueProduct($start, $end),
            'revenue_per_site'        => $this->getRevenuePerSite($start, $end),
        ];
    }

    private function getMostSoldProduct(Carbon $start, Carbon $end): ?array
    {
        $item = OrderItem::with('product')
            ->whereHas('order', fn($q) => $q->whereBetween('created_at', [$start, $end]))
            ->select('product_id', DB::raw('SUM(quantity) as total_qty'))
            ->groupBy('product_id')
            ->orderByDesc('total_qty')
            ->first();

        if (!$item) return null;

        return [
            'name'     => $item->product->name,
            'quantity' => (int) $item->total_qty,
        ];
    }

    private function getLeastSoldProduct(Carbon $start, Carbon $end): ?array
    {
        $item = OrderItem::with('product')
            ->whereHas('order', fn($q) => $q->whereBetween('created_at', [$start, $end]))
            ->select('product_id', DB::raw('SUM(quantity) as total_qty'))
            ->groupBy('product_id')
            ->orderBy('total_qty')
            ->first();

        if (!$item) return null;

        return [
            'name'     => $item->product->name,
            'quantity' => (int) $item->total_qty,
        ];
    }

    private function getHighestRevenueProduct(Carbon $start, Carbon $end): ?array
    {
        $item = OrderItem::with('product')
            ->whereHas('order', fn($q) => $q->whereBetween('created_at', [$start, $end]))
            ->select('product_id', DB::raw('SUM(quantity * unit_price) as revenue'))
            ->groupBy('product_id')
            ->orderByDesc('revenue')
            ->first();

        if (!$item) return null;

        return [
            'name'    => $item->product->name,
            'revenue' => (float) $item->revenue,
        ];
    }

    private function getLowestRevenueProduct(Carbon $start, Carbon $end): ?array
    {
        $item = OrderItem::with('product')
            ->whereHas('order', fn($q) => $q->whereBetween('created_at', [$start, $end]))
            ->select('product_id', DB::raw('SUM(quantity * unit_price) as revenue'))
            ->groupBy('product_id')
            ->orderBy('revenue')
            ->first();

        if (!$item) return null;

        return [
            'name'    => $item->product->name,
            'revenue' => (float) $item->revenue,
        ];
    }

    private function getRevenuePerSite(Carbon $start, Carbon $end): array
    {
        return Order::with('site')
            ->whereBetween('created_at', [$start, $end])
            ->select('site_id', DB::raw('SUM(total) as revenue'))
            ->groupBy('site_id')
            ->get()
            ->map(fn($o) => [
                'site'    => $o->site->name,
                'revenue' => (float) $o->revenue,
            ])
            ->values()
            ->toArray();
    }
}
