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
                <div class="col-lg-12">
                    <div class="row sort-row">
                        <div class="col-lg-2 col-md-12 mb-3 sort-div">
                            <i class="fas fa-sort-amount-down"></i>
                            <select name="sort" id="sort">
                                <option value="-id" @if($sort=="-id") selected @endif>Sort by Newest</option>
                                <option value="id" @if($sort=='id') selected @endif>Sort by Oldest</option>
                                <option value="title" @if($sort=="title") selected @endif>Sort by A-Z</option>
                                <option value="-title" @if($sort=="-title") selected @endif>Sort by Z-A</option>
                            </select>
                        </div>
                    </div>
                </div>

                <div class="col-lg-3">

                    <div class="filter-holder">
                        <hr>

                        <button class="accordion active">Other Filter</button>
                        <div class="panel">
                            <ul>
                                <li>
                                    <label for="filter_check">
                                    <input type="checkbox" id="filter_check" name="filter"  @if($favourite) checked @endif>
                                        My Favourite Documents
                                    </label>
                                </li>
                            </ul>
                        </div>
                        <hr>

                        @if(count($languages) > 0)
                        <button class="accordion active">Language</button>
                        <div class="panel">
                            <ul>

                                @foreach($languages as $languages)
                                <li>
                                    <label for="language{{$languages->id}}">
                                        <input type="checkbox" name="language" id="language{{$languages->id}}" value="{{$languages->id}}" @if(in_array($languages->id, $selected_languages)) checked @endif>
                                        {{$languages->name}}
                                    </label>
                                </li>
                                @endforeach

                            </ul>
                        </div>
                        <hr>
                        @endif


                    </div>
                    <div class="text-left">
                        <button id="filter_button" class="filter_button"> Apply </button>
                        <a href="{{route('content_document')}}" class="filter_button"> Clear </a>
                    </div>

                </div>

                <div class="col-lg-9">

                    <div class="row">

                        @if($documents->count() > 0)

                        @foreach($documents->items() as $document)
                        <div class="col-lg-4 col-sm-12">
                            <a class="media-href" title="{{$document->title}}" href="{{route('content_document_view', $document->uuid)}}">
                                <div class="img-holder">
                                    <img class="icon-img" src="{{Vite::asset('resources/images/pdf.webp')}}" alt="">
                                </div>
                                <div class="media-holder">
                                    <h5>{{$document->title}}</h5>
                                    <p class="desc">{{$document->description_unformatted}}</p>
                                    {{-- <p>Format : {{$document->file_format()}}</p> --}}
                                    @if($document->languages->count()>0)
                                    <p>Language : {{$document->Languages->pluck('name')->implode(', ')}}
                                    </p>
                                    @endif
                                    <p>Pages : {{$document->page_number}}</p>
                                    <p>Uploaded : {{$document->created_at->diffForHumans()}}</p>
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
                <div class="col-lg-9 my-4 nav-flex-direction-end">

                    {{ $documents->links('pagination::bootstrap-4') }}

                </div>
            </div>

        </div>

    </div>
</div>



@stop

@section('javascript')
<script src="{{ asset('main/js/plugins/axios.min.js') }}"></script>

@include('pages.main.content.common.search_js', ['search_url'=>route('content_document_search_query')])
@include('pages.main.content.common.multimedia_search_handler', ['search_url'=>route('content_document'), 'allow_language' => true, 'allow_sort' => true, 'allow_favourite' => true])

@include('pages.main.content.common.accordian_js')


@stop
