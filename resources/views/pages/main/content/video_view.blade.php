@extends('layouts.main.index')

@section('content')

@include('includes.main.sub_menu')


<div class="main-content-wrapper">
    @if($video->contentVisible())
    <div class="main-video-container" >
        <div class="plyr__video-embed" id="player">
            <iframe
                @if(strpos($video->video,'vimeo') !== false)
                src="{{$video->video}}?loop=false&amp;byline=false&amp;portrait=false&amp;title=false&amp;speed=true&amp;transparent=0&amp;gesture=media"
                @else
                src="{{$video->video}}?origin=https://plyr.io&amp;iv_load_policy=3&amp;modestbranding=1&amp;playsinline=1&amp;showinfo=0&amp;rel=0&amp;enablejsapi=1"
                @endif
                class="h-100"
                allowfullscreen
                allowtransparency
                allow="autoplay"
            ></iframe>
        </div>
    </div>
    @else
    @include('pages.main.content.common.denied_img', ['text'=>'video'])
    @endif
    <hr/>
    <div class="container">
        <div class="row action-button-row">
            <div class="col-sm-12">
                <div class="info-content">
                    <h5>{{$video->title}}</h5>
                </div>
            </div>
            <div class="col-sm-auto">
                <div class="info-content">
                    {{-- <p><span id="view_count">{{$video->views}} views</span> <span id="favourite_count">{{$video->favourites}} favourites</span></p> --}}
                    <p><span id="view_count">{{$video->views}} views</span></p>
                </div>
            </div>
            <div class="col-lg-5 col-md-6 col-sm-12 action-button-wrapper">
                <a href="{{route('content_video_makeFavourite',$video->uuid)}}" class="action-btn make-favourite-button">
                    @if($video->markedFavorite())
                    <i class="fas fa-heart-broken"></i>
                    @else
                    <i class="far fa-heart"></i>
                    @endif
                </a>
                <button class="action-btn report-button" data-toggle="modal" data-target="#reportModal" id="reportModalBtn"><i class="far fa-flag"></i> </button>
            </div>
        </div>
    </div>
    @if($video->description_unformatted)
    <hr/>
    <div class="container info-container info-major-content">
        <h6>Description</h6>
        {!!$video->description!!}
    </div>
    @endif
    <hr/>
    <div class="container info-container">
    @if($video->deity)<p>Deity : <b>{{$video->deity}}</b></p>@endif
    @if($video->languages->count()>0)
    <p>Language :
    @foreach ($video->languages as $languages)
        <b>{{$languages->name}}</b>,
    @endforeach
    </p>
    @endif
    <p>Uploaded : <b>{{$video->time_elapsed()}}</b></p>
    @if(count($video->getTagsArray())>0)
    <p>Tags :
    @foreach($video->getTagsArray() as $tag)
    <span class="hashtags">#{{$tag}}</span>
    @endforeach
    </p>
    @endif
    </div>


    @include('pages.main.content.common.request_access_modal')

    @include('pages.main.content.common.report_modal', ['text'=>'video'])

</div>

@stop

@section('javascript')
<script src="{{ asset('main/js/plugins/just-validate.production.min.js') }}"></script>
<script src="{{ asset('main/js/plugins/axios.min.js') }}"></script>
<script src="{{ asset('main/js/plugins/plyr.js') }}"></script>

<script nonce="{{ csp_nonce() }}">
    $(function () {
        $('[data-toggle="tooltip"]').tooltip()
    })
</script>

@if(!$video->contentVisible())
    @include('pages.main.content.common.request_access_form_js', ['url'=>route('content_video_requestAccess', $video->uuid)])
@endif
@include('pages.main.content.common.report_form_js', ['url'=>route('content_video_report', $video->uuid)])
@include('pages.main.content.common.reload_captcha_js')


@if($video->contentVisible())
<script nonce="{{ csp_nonce() }}">
const controls = [
    'play-large', // The large play button in the center
    'restart', // Restart playback
    'rewind', // Rewind by the seek time (default 10 seconds)
    'play', // Play/pause playback
    'fast-forward', // Fast forward by the seek time (default 10 seconds)
    'progress', // The progress bar and scrubber for playback and buffering
    'current-time', // The current time of playback
    'duration', // The full duration of the media
    'mute', // Toggle mute
    'volume', // Volume control
    'captions', // Toggle captions
    'settings', // Settings menu
    'pip',
    'airplay',
    'fullscreen'
];

// const player = new Plyr('#player', {
//     controls,
//     ratio: '16:9'
// });
</script>
@endif

@include('pages.main.content.common.dashboard_search_handler', ['search_url'=>route('content_dashboard')])

@include('pages.main.content.common.search_js', ['search_url'=>route('content_search_query')])

@stop
