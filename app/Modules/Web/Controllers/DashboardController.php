<?php

namespace App\Modules\Web\Controllers;

use App\Http\Controllers\Controller;
use App\Modules\SearchHistories\Models\SearchHistory;
use App\Modules\Web\Services\DashboardService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class DashboardController extends Controller
{
    public function __construct(private DashboardService $dashboardService){}

    public function index(Request $request)
    {
        $data = $this->dashboardService->paginate($request->total ?? 12);
        return view('pages.main.content.dashboard')
        ->with('breadcrumb','Content Library')
        ->with('data', $data)
        ->with([
            'sort' => $request->query('sort') ?? 'type',
            'selected_types' => explode('_', ($request->query('filter')['type'] ?? '')) ?? [],
        ]);
    }
}