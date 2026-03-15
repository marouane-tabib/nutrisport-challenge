<?php

namespace App\Services;

use App\Services\BaseService;
use App\Models\Order;
use App\Models\OrderItem;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

class ReportService extends BaseService
{
    /**
     * Generate a comprehensive daily report with sales and revenue analytics.
     *
     * @param Carbon|null $date The date to generate the report for (defaults to yesterday)
     * @return array The report containing most/least sold products, revenue data, and per-site revenue
     */
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

    /**
     * Get the most sold product within a date range.
     *
     * @param Carbon $start The start date for the range
     * @param Carbon $end The end date for the range
     * @return array|null The product name and total quantity sold, or null if no sales
     */
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

    /**
     * Get the least sold product within a date range.
     *
     * @param Carbon $start The start date for the range
     * @param Carbon $end The end date for the range
     * @return array|null The product name and total quantity sold, or null if no sales
     */
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

    /**
     * Get the product with highest revenue within a date range.
     *
     * @param Carbon $start The start date for the range
     * @param Carbon $end The end date for the range
     * @return array|null The product name and total revenue, or null if no sales
     */
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

    /**
     * Get the product with lowest revenue within a date range.
     *
     * @param Carbon $start The start date for the range
     * @param Carbon $end The end date for the range
     * @return array|null The product name and total revenue, or null if no sales
     */
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

    /**
     * Get revenue breakdown per site within a date range.
     *
     * @param Carbon $start The start date for the range
     * @param Carbon $end The end date for the range
     * @return array Array of sites with their total revenue
     */
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
