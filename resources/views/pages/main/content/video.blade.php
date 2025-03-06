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
                @include('pages.main.content.common.sort', ['allow_title_sort' => true])

                <div class="col-lg-3">

                    @include('pages.main.content.common.filter', ['allow_language_filter' => true, 'favourite' => $favourite, 'languages' => $languages, 'selected_languages' => $selected_languages, 'url' => route('content_video')])

                </div>

                <div class="col-lg-9">

                    <div class="row">

                        @if($videos->count() > 0)

                        @foreach($videos->items() as $video)
                        <div class="col-lg-4 col-sm-12">
                            <a class="media-href" title="{{$video->title}}" href="{{route('content_video_view', $video->uuid)}}">
                                <div class="img-holder">
                                    @if(strpos($video->video,'vimeo') !== false)
                                    <img src="https://vumbnail.com/{{$video->getVideoId()}}.jpg" alt="">
                                    @else
                                    <img src="https://i3.ytimg.com/vi/{{$video->getVideoId()}}/maxresdefault.jpg" alt="">
                                    @endif
                                </div>
                                <div class="media-holder">
                                    <h5>{{$video->title}}</h5>
                                    <p class="desc">{{$video->description_unformatted}}</p>
                                    @if($video->languages->count()>0)
                                    <p>Language : {{$video->Languages->pluck('name')->implode(', ')}}
                                    </p>
                                    @endif
                                    <p>Uploaded : {{$video->created_at->diffForHumans()}}</p>
                                </div>
                            </a>
                        </div>
                        @endforeach

                        @else
                        <div class="col-lg-12 col-sm-12 text-center">
                            <h6>No items are available.</h6>
                        </div>
                        @endif

                    </div>
                </div>
                <div class="col-lg-3"></div>
                <div class="col-lg-9 my-4 nav-flex-direction-end pagination-mobile-container">

                    {{ $videos->links('pagination::bootstrap-4') }}

                </div>
            </div>

        </div>

    </div>
</div>



@stop

@section('javascript')
<script src="{{ asset('main/js/plugins/axios.min.js') }}"></script>

@include('pages.main.content.common.search_js', ['search_url'=>route('content_video_search_query')])
@include('pages.main.content.common.multimedia_search_handler', ['search_url'=>route('content_video'), 'allow_language' => true, 'allow_sort' => true, 'allow_favourite' => true])


@include('pages.main.content.common.accordian_js')


@stop
