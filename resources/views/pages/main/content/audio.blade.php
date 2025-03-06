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
                    @include('pages.main.content.common.filter', ['allow_language_filter' => true, 'favourite' => $favourite, 'languages' => $languages, 'selected_languages' => $selected_languages, 'url' => route('content_audio')])

                </div>

                <div class="col-lg-9">

                    <div class="row">

                        @if($audios->count() > 0)

                        @foreach($audios->items() as $audio)
                        <div class="col-lg-4 col-sm-12">
                            <a class="media-href" title="{{$audio->title}}" href="{{route('content_audio_view', $audio->uuid)}}">
                                <div class="img-holder">
                                    <img class="icon-img" src="{{Vite::asset('resources/images/audio-book.webp')}}" alt="">
                                </div>
                                <div class="media-holder">
                                    <h5>{{$audio->title}}</h5>
                                    <p class="desc">{{$audio->description_unformatted}}</p>
                                    {{-- <p>Format : {{$audio->file_format()}}</p> --}}
                                    @if($audio->Languages->count()>0)
                                    <p>Language : {{$audio->Languages->pluck('name')->implode(', ')}}
                                    </p>
                                    @endif
                                    <p>Duration : {{$audio->duration}}</p>
                                    <p>Uploaded : {{$audio->created_at->diffForHumans()}}</p>
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

                    {{ $audios->links('pagination::bootstrap-4') }}

                </div>
            </div>

        </div>

    </div>
</div>



@stop

@section('javascript')
<script src="{{ asset('main/js/plugins/axios.min.js') }}"></script>

@include('pages.main.content.common.search_js', ['search_url'=>route('content_audio_search_query')])
@include('pages.main.content.common.multimedia_search_handler', ['search_url'=>route('content_audio'), 'allow_language' => true, 'allow_sort' => true, 'allow_favourite' => true])

@include('pages.main.content.common.accordian_js')

@stop
