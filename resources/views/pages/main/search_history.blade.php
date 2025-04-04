@extends('layouts.main.index')

@section('content')

@include('includes.main.sub_menu')

@include('includes.main.breadcrumb')

<div class="contact-page-wrapper">

    <div class="contact-form-area section-space--ptb_90">
        <div class="container">

            <table class="table table-striped">
                <thead class="thead-dark">
                    <tr>
                        <th scope="col">#</th>
                        <th scope="col">Search Query</th>
                        <th scope="col">DateTime</th>
                        <th scope="col">Action</th>
                    </tr>
                </thead>
                <tbody>
                    @if($search_history->count() > 0)
                    @foreach($search_history->items() as $key=>$value)
                    <tr>
                        <th scope="row">{{$key+1}}</th>
                        <td>{{$value->search}}</td>
                        <td>{{$value->created_at}}</td>
                        @if($value->screen==1)
                        <td><a target="_blank" href="{{route('content_dashboard')}}?filter[search]={{$value->search}}" class="btn btn-warning">View Search Results</a></td>
                        @elseif($value->screen==2)
                        <td><a target="_blank" href="{{route('content_audio')}}?filter[search]={{$value->search}}" class="btn btn-warning">View Search Results</a></td>
                        @elseif($value->screen==3)
                        <td><a target="_blank" href="{{route('content_document')}}?filter[search]={{$value->search}}" class="btn btn-warning">View Search Results</a></td>
                        @elseif($value->screen==4)
                        <td><a target="_blank" href="{{route('content_image')}}?filter[search]={{$value->search}}" class="btn btn-warning">View Search Results</a></td>
                        @else
                        <td><a target="_blank" href="{{route('content_video')}}?filter[search]={{$value->search}}" class="btn btn-warning">View Search Results</a></td>
                        @endif
                    </tr>
                    @endforeach
                    @endif
                </tbody>
            </table>
            <div class="d-flex justify-content-center">
                {{ $search_history->links('pagination::bootstrap-4') }}
            </div>

        </div>
    </div>
</div>

@stop

@section('javascript')
<script src="{{ asset('main/js/plugins/axios.min.js') }}"></script>
@include('pages.main.content.common.search_js', ['search_url'=>route('content_search_query')])
@include('pages.main.content.common.dashboard_search_handler', ['search_url'=>route('content_dashboard'), 'allow_sort' => false])
@stop
