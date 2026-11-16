<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Product;
use App\Models\Shop;
use App\Models\User;
use App\Models\VisitorVisit;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\View\View;

class AdminDashboardController extends Controller
{
    public function __invoke(): View
    {
        $today = today();
        $todayString = $today->toDateString();
        $dailyVisitorCounts = VisitorVisit::query()
            ->where('scope_key', 'site')
            ->whereBetween('visited_on', [$today->copy()->subDays(6)->toDateString(), $todayString])
            ->selectRaw('visited_on, COUNT(*) AS visitors')
            ->groupBy('visited_on')
            ->get()
            ->keyBy(fn (VisitorVisit $visit): string => $visit->visited_on->toDateString());

        return view('admin.dashboard', [
            'userCount' => User::count(),
            'shopCount' => Shop::count(),
            'productCount' => Product::count(),
            'todayVisitorCount' => VisitorVisit::query()
                ->where('scope_key', 'site')
                ->where('visited_on', $todayString)
                ->count(),
            'dailyVisitorCounts' => collect(range(6, 0))
                ->map(function (int $daysAgo) use ($today, $dailyVisitorCounts): array {
                    $date = $today->copy()->subDays($daysAgo);
                    $count = $dailyVisitorCounts->get($date->toDateString());

                    return [
                        'date' => $date,
                        'count' => (int) ($count?->visitors ?? 0),
                    ];
                }),
            'shopVisitors' => Shop::query()
                ->withCount([
                    'visitorVisits as visitors_today' => fn (Builder $query) => $query->where('visited_on', $todayString),
                ])
                ->orderByDesc('visitors_today')
                ->orderBy('name')
                ->limit(10)
                ->get(),
        ]);
    }
}
