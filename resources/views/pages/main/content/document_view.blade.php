@extends('layouts.main.index')

@section('css')

<style nonce="{{ csp_nonce() }}">
    .iframe-h{
        height: 75dvh !important;
    }
</style>
@stop

@section('content')

@include('includes.main.sub_menu')


<div class="main-content-wrapper">
    @if($document->contentVisible())
        <div class="main-image-container" id="image-container"
            >
            <iframe src="{{route('content_document_reader', ['uuid' => $document->uuid])}}" class="w-100 iframe-h" frameborder="0"></iframe>
        </div>
    @else
        @include('pages.main.content.common.denied_img', ['text'=>'document'])
    @endif
    <hr />
    <div class="container">
        <div class="row action-button-row">
            <div class="col-sm-12">
                <div class="info-content">
                    <h5>{{$document->title}}</h5>
                </div>
            </div>
            <div class="col-sm-auto">
                <div class="info-content">
                    {{-- <p><span id="view_count">{{$document->views}} views</span> <span
                            id="favourite_count">{{$document->favourites}} favourites</span></p> --}}
                    <p><span id="view_count">{{$document->views}} views</span> </p>
                </div>
            </div>
            <div class="col-lg-5 col-md-6 col-sm-12 action-button-wrapper">
                <a href="{{route('content_document_makeFavourite',$document->uuid)}}"
                    class="action-btn make-favourite-button">
                    @if($document->markedFavorite())
                    <i class="fas fa-heart-broken"></i>
                    @else
                    <i class="far fa-heart"></i>
                    @endif
                </a>
                <button class="action-btn report-button" data-toggle="modal" data-target="#reportModal" id="reportModalBtn"><i
                        class="far fa-flag"></i> </button>
            </div>
        </div>
    </div>
    @if($document->description_unformatted)
    <hr />
    <div class="container info-container info-major-content">
        <h6>Description</h6>
        {!!$document->description!!}
    </div>
    @endif
    <hr />
    <div class="container info-container">
        @if($document->deity)<p>Deity : <b>{{$document->deity}}</b></p>@endif
        @if($document->languages->count()>0)
        <p>Language : {!!$document->Languages->pluck('name')->map(function($name){return '<b>'.$name.'</b>';})->implode(', ')!!}
        </p>
        @endif
        <p>Number of Pages : <b>{{$document->page_number}}</b></p>
        <p>Uploaded : <b>{{$document->created_at->diffForHumans()}}</b></p>
        @if(count($document->tags_array)>0)
        <p>Tags :
        @foreach($document->tags_array as $tag)
        <span class="hashtags">#{{$tag}}</span>
        @endforeach
        </p>
        @endif
    </div>


    @include('pages.main.content.common.request_access_modal')

    @include('pages.main.content.common.report_modal', ['text'=>'document'])

</div>

@stop

@section('javascript')
<script src="{{ asset('admin/js/pages/just-validate.production.min.js') }}"></script>
<script src="{{ asset('main/js/plugins/axios.min.js') }}"></script>
{!! NoCaptcha::renderJs() !!}

<script nonce="{{ csp_nonce() }}">
$(function() {
    $('[data-toggle="tooltip"]').tooltip()
})
</script>
@if(!$document->contentVisible())
    @include('pages.main.content.common.request_access_form_js', ['url'=>route('content_document_requestAccess', $document->uuid)])
@endif

@include('pages.main.content.common.report_form_js', ['url'=>route('content_document_report', $document->uuid)])

@include('pages.main.content.common.search_js', ['search_url'=>route('content_document_search_query')])


@stop
