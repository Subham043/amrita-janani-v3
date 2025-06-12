<?php

namespace App\Modules\Analytics\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Support\Carbon;
use Spatie\Analytics\Facades\Analytics;
use Spatie\Analytics\Period;

class AnalyticsController extends Controller
{
    public function __construct(){}

    public function index(){
        $visitorsPageView = Analytics::fetchVisitorsAndPageViews(Period::days(7), 20);
        $mostVisitedPages = Analytics::fetchMostVisitedPages(Period::days(7), 20);
        $topReferrers = Analytics::fetchTopReferrers(Period::days(7), 20);
        $topBrowsers = Analytics::fetchTopBrowsers(Period::days(7), 20);
        $topCountries = Analytics::fetchTopCountries(Period::days(7), 20);
        $topOperatingSystems = Analytics::fetchTopOperatingSystems(Period::days(7), 20);

        return view('pages.admin.analytic.index')
        ->with([
            'visitorsPageView'=>$visitorsPageView,
            'mostVisitedPages'=>$mostVisitedPages,
            'topReferrers'=>$topReferrers,
            'topBrowsers'=>$topBrowsers,
            'topCountries'=>$topCountries,
            'topOperatingSystems'=>$topOperatingSystems
        ]);
    }

}
