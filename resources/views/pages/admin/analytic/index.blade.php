@extends('layouts.admin.dashboard')


@section('css')
<style nonce="{{ csp_nonce() }}">
    .width-sm{
        width: 130px;
    }
</style>
@stop


@section('content')

<div class="page-content">
    <div class="container-fluid">

        @include('includes.admin.page_title', [
            'page_name' => "Analytics",
            'current_page' => "List",
        ])

        <div class="row">
            <div class="col-lg-12">
                <div class="card">
                    <div class="card-header d-flex align-items-center justify-content-between">
                        <h4 class="card-title mb-0">Analytics</h4>
                        {{-- <div class="flex-shrink-0 d-none" id="enquiry_multiple_action_container">
                            <button id="remove_multiple_enquiries" type="button" class="btn btn-danger">Delete</button>

                        </div> --}}
                        <div class="col-sm ">
                            <form  method="get" action="{{route('analytics')}}" class="col-sm-auto d-flex gap-2 justify-content-end">
                                <div class="d-flex justify-content-sm-end align-items-center">
                                    <div class="search-box">
                                        <p class="m-0">Last:</p>
                                    </div>
                                </div>
                                <div class="d-flex justify-content-sm-end">
                                    <select name="number" class="form-control search-handler">
                                        @for($i=1; $i<=30; $i++)
                                        <option value="{{$i}}" @if(($filter_number==$i)) selected @endif>{{$i}}</option>
                                        @endfor
                                    </select>
                                </div>
                                <div class="d-flex justify-content-sm-end">
                                    <select name="type" class="form-control search-handler">
                                        <option value="days" @if(($filter_type=='days')) selected @endif>Days</option>
                                        <option value="months" @if(($filter_type=='months')) selected @endif>Months</option>
                                        <option value="years" @if(($filter_type=='years')) selected @endif>Years</option>
                                    </select>
                                </div>
                                <button type="submit"
                                    class="btn btn-dark add-btn">Filter</button>
                            </form>
                        </div>
                    </div><!-- end card header -->

                    <div class="card-body">
                        <div id="customerList">
                            <div class="row g-4 mb-3">
                                <div class="col-sm-12 text-center">
                                    <div>
                                        <h4>Page Views & Visitors For Last {{ $filter_number }} {{ $filter_type }}</h4>
                                    </div>
                                </div>
                            </div>
                            <div class="table-responsive table-card mt-3">
                                @if(count($visitorsPageView) > 0)
                                <table class="table align-middle table-nowrap" id="customerTable">
                                    <thead class="table-light">
                                        <tr>
                                            <th class="sort" data-sort="customer_name">Page Title</th>
                                            <th class="sort width-sm" data-sort="status">Active Users</th>
                                            <th class="sort width-sm" data-sort="customer_name">Screen Page Views</th>
                                            </tr>
                                    </thead>
                                    <tbody class="list form-check-all">

                                        @foreach ($visitorsPageView as $item)
                                        <tr>
                                            <td class="customer_name">{{$item['pageTitle']}}</td>
                                            <td class="customer_name width-sm text-center">{{$item['activeUsers']}}</td>
                                            <td class="customer_name width-sm text-center">{{$item['screenPageViews']}}</td>
                                        </tr>
                                        @endforeach

                                    </tbody>
                                </table>
                                @else
                                    @include('includes.admin.no_result')
                                @endif
                            </div>

                        </div>
                        
                        <div>
                            <div class="row g-4 mb-3">
                                <div class="col-sm-12 text-center">
                                    <div>
                                        <h4>Most Visited Pages For Last {{ $filter_number }} {{ $filter_type }}</h4>
                                    </div>
                                </div>
                            </div>
                            
                            <div class="table-responsive table-card mt-3">
                                @if(count($mostVisitedPages) > 0)
                                <table class="table align-middle table-nowrap" id="customerTable">
                                    <thead class="table-light">
                                        <tr>
                                            <th class="sort" data-sort="customer_name">Page Title</th>
                                            <th class="sort" data-sort="status">Full Page Url</th>
                                            <th class="sort width-sm" data-sort="customer_name">Screen Page Views</th>
                                            </tr>
                                    </thead>
                                    <tbody class="list form-check-all">

                                        @foreach ($mostVisitedPages as $item)
                                        <tr>
                                            <td class="customer_name">{{$item['pageTitle']}}</td>
                                            <td class="customer_name"><a href="{{$item['fullPageUrl']}}" target="_blank" rel="noopener noreferrer">{{$item['fullPageUrl']}}</a></td>
                                            <td class="customer_name width-sm text-center">{{$item['screenPageViews']}}</td>
                                        </tr>
                                        @endforeach

                                    </tbody>
                                </table>
                                @else
                                    @include('includes.admin.no_result')
                                @endif
                            </div>

                        </div>

                        <div>
                            <div class="row g-4 mb-3">
                                <div class="col-sm-12 text-center">
                                    <div>
                                        <h4>Top Referrers For Last {{ $filter_number }} {{ $filter_type }}</h4>
                                    </div>
                                </div>
                            </div>

                            <div class="table-responsive table-card mt-3">
                                @if(count($topReferrers) > 0)
                                <table class="table align-middle table-nowrap" id="customerTable">
                                    <thead class="table-light">
                                        <tr>
                                            <th class="sort" data-sort="status">Page Referrer</th>
                                            <th class="sort width-sm" data-sort="customer_name">Screen Page Views</th>
                                            </tr>
                                    </thead>
                                    <tbody class="list form-check-all">

                                        @foreach ($topReferrers as $item)
                                        <tr>
                                            <td class="customer_name"><a href="{{$item['pageReferrer']}}" target="_blank" rel="noopener noreferrer">{{$item['pageReferrer']}}</a></td>
                                            <td class="customer_name width-sm text-center">{{$item['screenPageViews']}}</td>
                                        </tr>
                                        @endforeach

                                    </tbody>
                                </table>
                                @else
                                    @include('includes.admin.no_result')
                                @endif
                            </div>

                        </div>

                        <div>
                            <div class="row g-4 mb-3">
                                <div class="col-sm-12 text-center">
                                    <div>
                                        <h4>Top Countries For Last {{ $filter_number }} {{ $filter_type }}</h4>
                                    </div>
                                </div>
                            </div>

                            <div class="table-responsive table-card mt-3">
                                @if(count($topCountries) > 0)
                                <table class="table align-middle table-nowrap" id="customerTable">
                                    <thead class="table-light">
                                        <tr>
                                            <th class="sort" data-sort="status">Country</th>
                                            <th class="sort width-sm" data-sort="customer_name">Screen Page Views</th>
                                            </tr>
                                    </thead>
                                    <tbody class="list form-check-all">

                                        @foreach ($topCountries as $item)
                                        <tr>
                                            <td class="customer_name">{{$item['country']}}</td>
                                            <td class="customer_name width-sm text-center">{{$item['screenPageViews']}}</td>
                                        </tr>
                                        @endforeach

                                    </tbody>
                                </table>
                                @else
                                    @include('includes.admin.no_result')
                                @endif
                            </div>

                        </div>

                        <div>
                            <div class="row g-4 mb-3">
                                <div class="col-sm-12 text-center">
                                    <div>
                                        <h4>Top Operating Systems For Last {{ $filter_number }} {{ $filter_type }}</h4>
                                    </div>
                                </div>
                            </div>

                            <div class="table-responsive table-card mt-3">
                                @if(count($topOperatingSystems) > 0)
                                <table class="table align-middle table-nowrap" id="customerTable">
                                    <thead class="table-light">
                                        <tr>
                                            <th class="sort" data-sort="status">Operating System</th>
                                            <th class="sort width-sm" data-sort="customer_name">Screen Page Views</th>
                                            </tr>
                                    </thead>
                                    <tbody class="list form-check-all">

                                        @foreach ($topOperatingSystems as $item)
                                        <tr>
                                            <td class="customer_name">{{$item['operatingSystem']}}</td>
                                            <td class="customer_name width-sm text-center">{{$item['screenPageViews']}}</td>
                                        </tr>
                                        @endforeach

                                    </tbody>
                                </table>
                                @else
                                    @include('includes.admin.no_result')
                                @endif
                            </div>

                        </div>

                        <div>
                            <div class="row g-4 mb-3">
                                <div class="col-sm-12 text-center">
                                    <div>
                                        <h4>Top Browsers For Last {{ $filter_number }} {{ $filter_type }}</h4>
                                    </div>
                                </div>
                            </div>

                            <div class="table-responsive table-card mt-3">
                                @if(count($topBrowsers) > 0)
                                <table class="table align-middle table-nowrap" id="customerTable">
                                    <thead class="table-light">
                                        <tr>
                                            <th class="sort" data-sort="status">Browser</th>
                                            <th class="sort width-sm" data-sort="customer_name">Screen Page Views</th>
                                            </tr>
                                    </thead>
                                    <tbody class="list form-check-all">

                                        @foreach ($topBrowsers as $item)
                                        <tr>
                                            <td class="customer_name">{{$item['browser']}}</td>
                                            <td class="customer_name width-sm text-center">{{$item['screenPageViews']}}</td>
                                        </tr>
                                        @endforeach

                                    </tbody>
                                </table>
                                @else
                                    @include('includes.admin.no_result')
                                @endif
                            </div>

                        </div>

                    </div><!-- end card -->
                </div>
                <!-- end col -->
            </div>
            <!-- end col -->
        </div>
        <!-- end row -->

    </div>
</div>

@stop
