@extends('layouts.main.index')

@section('css')

<style nonce="{{ csp_nonce() }}">
    .panel{
        max-height: 100%;
        height:auto;
    }
</style>
@stop

@section('content')

@include('includes.main.sub_menu')

@include('includes.main.breadcrumb')

<div class="content-holder">
    <div class="container content-container pt-0">
        <div class="media-container">
            <div class="row">
                @include('pages.main.content.common.sort', ['allow_title_sort' => false])

                <div class="col-lg-12 col-md-12">

                    <div class="row">

                        @if($data->count() > 0)

                        @foreach($data->items() as $item)
                        <div class="col-lg-3 col-md-6 col-sm-12">
                            <a class="media-href" title="{{$item->title}}" href="{{$item->route}}">
                                <div class="img-holder">
                                    <img @class(['icon-img' => ($item->type == 'DOCUMENT' || $item->type == 'AUDIO')]) src="{{$item->file_link}}" alt="">
                                </div>
                                <div class="media-holder">
                                    <h5>{{$item->title}}</h5>
                                    <p class="desc">{{$item->description_unformatted}}</p>
                                    <p>Format : <b>{{$item->type}}</b></p>
                                    <p>Uploaded : <b>{{$item->created_at->diffForHumans()}}</b></p>
                                </div>
                            </a>
                        </div>
                        @endforeach

                        @else
                        <div class="col-lg-12 col-sm-12 text-left">
                            <h6>No items are available.</h6>
                        </div>

                        @endif

                    </div>
                </div>
                <div class="col-lg-12 col-sm-12 my-4 nav-flex-direction-end pagination-overflow">

                    {{ $data->links('pagination::bootstrap-4') }}

                </div>
            </div>

        </div>

    </div>
</div>



@stop

@section('javascript')
<script src="{{ asset('main/js/plugins/axios.min.js') }}"></script>
@include('pages.main.content.common.search_js', ['search_url'=>route('content_search_query')])
@include('pages.main.content.common.accordian_js')
@include('pages.main.content.common.dashboard_search_handler', [
    'search_url' => route('content_dashboard'),
    'allow_sort' => true
])


@stop
