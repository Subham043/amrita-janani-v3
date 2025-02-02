@extends('layouts.main.index')

@section('css')

@if(($audio->restricted==0) || (!empty($audioAccess) && $audioAccess->status==1))
<style nonce="{{ csp_nonce() }}">
    footer.footer-area {
        padding-bottom:6vh;
    }
</style>
@endif

@stop

@section('content')

@include('includes.main.sub_menu')


<div class="main-content-wrapper">
    @if($audio->contentVisible())
    <div class="container">
        <div class="main-audio-container">
        <img src="{{Vite::asset('resources/images/audio-book.webp')}}" alt="">
            <audio id="player" controls>
                <source src="{{route('content_audio_file',$audio->uuid)}}" type="audio/{{$audio->file_format()}}" />
            </audio>
        </div>
    </div>
    @else
    @include('pages.main.content.common.denied_img', ['text'=>'audio'])
    @endif
    <hr/>
    <div class="container">
        <div class="row action-button-row">
            <div class="col-sm-12">
                <div class="info-content">
                    <h5>{{$audio->title}}</h5>
                </div>
            </div>
            <div class="col-sm-auto">
                <div class="info-content">
                    {{-- <p><span id="view_count">{{$audio->views}} views</span> <span id="favourite_count">{{$audio->favourites}} favourites</span></p> --}}
                    <p><span id="view_count">{{$audio->views}} views</span> </p>
                </div>
            </div>
            <div class="col-lg-5 col-md-6 col-sm-12 action-button-wrapper">
                <a href="{{route('content_audio_makeFavourite',$audio->uuid)}}" class="action-btn make-favourite-button">
                    @if($audio->markedFavorite())
                    <i class="fas fa-heart-broken"></i>
                    @else
                    <i class="far fa-heart"></i>
                    @endif
                </a>
                <button class="action-btn report-button" data-toggle="modal" data-target="#reportModal" id="reportModalBtn"><i class="far fa-flag"></i> </button>
            </div>
        </div>
    </div>
    @if($audio->description_unformatted)
    <hr/>
    <div class="container info-container info-major-content">
        <h6>Description</h6>
        {!!$audio->description!!}
    </div>
    @endif
    <hr/>
    <div class="container info-container">
    @if($audio->languages->count()>0)
    <p>Language :
    @foreach ($audio->languages as $languages)
        <b>{{$languages->name}}</b>,
    @endforeach
    </p>
    @endif
    <p>Duration : <b>{{$audio->duration}}</b></p>
    @if($audio->deity)<p>Deity : <b>{{$audio->deity}}</b></p>@endif
    <p>Uploaded : <b>{{$audio->time_elapsed()}}</b></p>
    @if(count($audio->getTagsArray())>0)
    <p>Tags :
    @foreach($audio->getTagsArray() as $tag)
    <span class="hashtags">#{{$tag}}</span>
    @endforeach
    </p>
    @endif
    </div>


    @include('pages.main.content.common.request_access_modal')

    @include('pages.main.content.common.report_modal', ['text'=>'audio'])

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
@if(!$audio->contentVisible())
    @include('pages.main.content.common.request_access_form_js', ['url'=>route('content_audio_requestAccess', $audio->uuid)])
@endif

@include('pages.main.content.common.report_form_js', ['url'=>route('content_audio_report', $audio->uuid)])


@include('pages.main.content.common.reload_captcha_js')

@if($audio->contentVisible())
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
];

const player = new Plyr('#player', {
    controls,
});
</script>
@endif

@include('pages.main.content.common.dashboard_search_handler', ['search_url'=>route('content_dashboard')])

@include('pages.main.content.common.search_js', ['search_url'=>route('content_search_query')])

@stop
