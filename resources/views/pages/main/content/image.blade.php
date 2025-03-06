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

                <div class="col-lg-3 col-md-12">
                    
                    @include('pages.main.content.common.filter', ['allow_language_filter' => false, 'favourite' => $favourite, 'url' => route('content_image')])

                </div>

                <div class="col-lg-9 col-md-12">

                    <div class="row">

                        @if($images->count() > 0)

                        @foreach($images->items() as $image)
                        <div class="col-lg-4 col-md-6 col-sm-12">
                            <a class="media-href" title="{{$image->title}}" href="{{route('content_image_view', $image->uuid)}}">
                                <div class="img-holder">
                                    <img src="{{$image->content_image_thumbnail_link}}" alt="">
                                </div>
                                <div class="media-holder">
                                    <h5>{{$image->title}}</h5>
                                    <p class="desc">{{$image->description_unformatted}}</p>
                                    {{-- <p>Format : <b>{{$image->file_format()}}</b></p> --}}
                                    <p>Uploaded : <b>{{$image->created_at->diffForHumans()}}</b></p>
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
                <div class="col-lg-3"></div>
                <div class="col-lg-9 my-4 nav-flex-direction-end pagination-mobile-container">

                    {{ $images->links('pagination::bootstrap-4') }}

                </div>
            </div>

        </div>

    </div>
</div>



@stop

@section('javascript')
<script src="{{ asset('main/js/plugins/axios.min.js') }}"></script>

@include('pages.main.content.common.search_js', ['search_url'=>route('content_image_search_query')])
@include('pages.main.content.common.multimedia_search_handler', ['search_url'=>route('content_image'), 'allow_language' => false, 'allow_sort' => true, 'allow_favourite' => true])


@include('pages.main.content.common.accordian_js')


@stop
