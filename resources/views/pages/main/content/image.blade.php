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

                <div class="col-lg-3 col-md-12">

                    <div class="filter-holder">
                        <hr>


                        <button class="accordion active">Filter</button>
                        <div class="panel">
                            <ul>
                                <li>
                                    <label for="filter_check">
                                        <input type="checkbox" id="filter_check" name="filter"  @if($favourite) checked @endif>
                                        My Favourite Images
                                    </label>
                                </li>
                            </ul>
                        </div>
                        <hr>


                    </div>
                    <div class="text-left">
                        <button id="filter_button" class="filter_button"> Apply </button>
                        <a href="{{route('content_image')}}" class="filter_button"> Clear </a>
                    </div>

                </div>

                <div class="col-lg-9 col-md-12">

                    <div class="row">

                        @if($images->count() > 0)

                        @foreach($images->items() as $image)
                        <div class="col-lg-4 col-md-6 col-sm-12">
                            <a class="media-href" title="{{$image->title}}" href="{{route('content_image_view', $image->uuid)}}">
                                <div class="img-holder">
                                    <img src="{{$image->content_image_link}}" alt="">
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
                <div class="col-lg-9 my-4 nav-flex-direction-end">

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
