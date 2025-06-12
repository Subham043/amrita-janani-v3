<?php

namespace App\Modules\Analytics\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Spatie\Analytics\Facades\Analytics;
use Spatie\Analytics\Period;

class AnalyticsController extends Controller
{
    public function __construct(){}

    public function index(Request $request){
        $filter_type = $request->query('type', 'days');
        $filter_number = (int) $request->query('number', 7);
        switch (strtolower($filter_type)) {
            case 'days':
                $period = Period::days($filter_number);
                break;
            case 'months':
                $period = Period::months($filter_number);
                break;
            case 'years':
                $period = Period::years($filter_number);
                break;
        }
        $visitorsPageView = Analytics::fetchVisitorsAndPageViews($period, 20);
        $mostVisitedPages = Analytics::fetchMostVisitedPages($period, 20);
        $topReferrers = Analytics::fetchTopReferrers($period, 20);
        $topBrowsers = Analytics::fetchTopBrowsers($period, 20);
        $topCountries = Analytics::fetchTopCountries($period, 20);
        $topOperatingSystems = Analytics::fetchTopOperatingSystems($period, 20);

        return view('pages.admin.analytic.index')
        ->with([
            'visitorsPageView'=>$visitorsPageView,
            'mostVisitedPages'=>$mostVisitedPages,
            'topReferrers'=>$topReferrers,
            'topBrowsers'=>$topBrowsers,
            'topCountries'=>$topCountries,
            'topOperatingSystems'=>$topOperatingSystems,
            'filter_type'=>$filter_type,
            'filter_number'=>$filter_number
        ]);
    }

}
