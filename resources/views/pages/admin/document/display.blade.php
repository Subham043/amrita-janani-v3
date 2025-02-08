@extends('layouts.admin.dashboard')


@section('css')
<link href="{{asset('main/dflip/css/dflip.min.css')}}" rel="stylesheet" type="text/css">
<!-- themify-icons.min.css is not required in version 2.0 and above -->
<link href="{{asset('main/dflip/css/themify-icons.min.css')}}" rel="stylesheet" type="text/css">
<style nonce="{{ csp_nonce() }}">
    #canvas_container {
        width: 100%;
        height: 550px;
        overflow: auto;
        position: relative;
    }

    #canvas_container {
    background: #333;
    text-align: center;
    border: solid 3px;
    }

    #pdf_controllers{
        width: 100%;
        background: #222;
        display:flex;
        justify-content: space-between;
        align-items: center;
        padding:10px 15px;
    }

    #pdf_controllers button{
        display:grid;
        place-items: center;
        outline: none;
        border: none;
        background:#96171c;
        color:white;
        border-radius:5px;
        padding:5px 10px;
    }

    #navigation_controls, #zoom_controls{
        display:flex;
        align-items:center;
    }
    #navigation_controls button, #zoom_controls button{
        margin:0 10px;
        min-width:30px;
        height:35px;
    }
    #navigation_controls input{
        max-width:30px;
        height:100%;
        margin:0;
        padding:0;
        text-align:center;
        outline:none;
        border:none;
        width: 30px;
        cursor: pointer;
    }
    #navigation_controls label{
        display:flex;
        align-items:center;
        margin:0;
        padding:0;
        height: 40px;
        width: 80px;
        background-color:white;
        outline:none;
        border:1px solid #eee;
        padding:5px;
        position:relative;
        text-align: center;
        color:black;
        border-radius: 5px;
    }
    #totalPageCount{
        /* color:white; */
        margin-left:10px
    }

    .iframe-h{
        height: 750px !important;
    }
</style>
@stop


@section('content')

<div class="page-content">
    <div class="container-fluid">

        @include('includes.admin.page_title', [
            'page_name' => "Document",
            'current_page' => "View",
        ])

        <div class="row">
            <div class="col-lg-12">
                <div class="card">
                    <div class="card-body">
                        <div class="row g-4 mb-3">
                        <div class="col-sm-auto">
                                <div>
                                    <a href="{{url()->previous()}}" type="button" class="btn btn-dark add-btn" id="create-btn"><i class="ri-arrow-go-back-line align-bottom me-1"></i> Go Back</a>
                                </div>
                            </div>
                            <div class="col-sm">
                                <div class="d-flex justify-content-sm-end">
                                    <a href="{{route('document_edit', $data->id)}}" type="button" class="btn btn-success add-btn me-2" id="create-btn"><i class="ri-edit-line align-bottom me-1"></i> Edit</a>
                                    <button type="button" class="btn btn-danger add-btn remove-item-btn" data-link="{{route('document_delete', $data->id)}}" id="create-btn"><i class="ri-delete-bin-line align-bottom me-1 pointer-events-none"></i> Delete</button>
                                </div>
                            </div>
                        </div>
                        <div class="text-muted">
                            <div class="pt-3 pb-3 border-top border-top-dashed border-bottom border-bottom-dashed mt-4">
                                <div class="row">

                                    <div class="col-lg-3 col-sm-6">
                                        <div>
                                            <p class="mb-2 text-uppercase fw-medium fs-13">Title :</p>
                                            <h5 class="fs-15 mb-0">{{$data->title}}</h5>
                                        </div>
                                    </div>
                                    <div class="col-lg-3 col-sm-6">
                                        <div>
                                            <p class="mb-2 text-uppercase fw-medium fs-13">Year :</p>
                                            <h5 class="fs-15 mb-0">{{$data->year}}</h5>
                                        </div>
                                    </div>
                                    @if($data->languages->count()>0)
                                    <div class="col-lg-3 col-sm-6">
                                        <div>
                                            <p class="mb-2 text-uppercase fw-medium fs-13">Languages :</p>
                                            @foreach ($data->languages as $languages)
                                                <div class="badge bg-secondary fs-12">{{$languages->name}}</div>
                                            @endforeach
                                        </div>
                                    </div>
                                    @endif
                                    <div class="col-lg-3 col-sm-6">
                                        <div>
                                            <p class="mb-2 text-uppercase fw-medium fs-13">Deity :</p>
                                            <h5 class="fs-15 mb-0">{{$data->deity}}</h5>
                                        </div>
                                    </div>

                                </div>
                            </div>
                            <div class="pt-3 pb-3 border-bottom border-bottom-dashed mt-4">
                                <div class="row">

                                    <div class="col-lg-3 col-sm-6">
                                        <div>
                                            <p class="mb-2 text-uppercase fw-medium fs-13">Version :</p>
                                            <h5 class="fs-15 mb-0">{{$data->version}}</h5>
                                        </div>
                                    </div>
                                    <div class="col-lg-3 col-sm-6">
                                        <div>
                                            <p class="mb-2 text-uppercase fw-medium fs-13">Uploaded By :</p>
                                            <h5 class="fs-15 mb-0">{{$data->getAdminName()}}</h5>
                                        </div>
                                    </div>
                                    <div class="col-lg-3 col-sm-6">
                                        <div>
                                            <p class="mb-2 text-uppercase fw-medium fs-13">Status :</p>
                                            @if($data->status == 1)
                                            <div class="badge bg-success fs-12">Active</div>
                                            @else
                                            <div class="badge bg-danger fs-12">Inactive</div>
                                            @endif
                                        </div>
                                    </div>
                                    <div class="col-lg-3 col-sm-6">
                                        <div>
                                            <p class="mb-2 text-uppercase fw-medium fs-13">Restricted :</p>
                                            @if($data->restricted == 1)
                                            <div class="badge bg-success fs-12">Yes</div>
                                            @else
                                            <div class="badge bg-danger fs-12">No</div>
                                            @endif
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="pt-3 pb-3 border-bottom border-bottom-dashed mt-4">
                                <div class="row">
                                    <div class="col-lg-3 col-sm-6">
                                        <div>
                                            <p class="mb-2 text-uppercase fw-medium fs-13">Total Number of Pages :</p>
                                            <h5 class="fs-15 mb-0">{{$data->page_number}}</h5>
                                        </div>
                                    </div>
                                    <div class="col-lg-3 col-sm-6">
                                        <div>
                                            <p class="mb-2 text-uppercase fw-medium fs-13">Total Favourites :</p>
                                            <h5 class="fs-15 mb-0">{{$data->favourites}}</h5>
                                        </div>
                                    </div>
                                    <div class="col-lg-3 col-sm-6">
                                        <div>
                                            <p class="mb-2 text-uppercase fw-medium fs-13">Total Views :</p>
                                            <h5 class="fs-15 mb-0">{{$data->views}}</h5>
                                        </div>
                                    </div>
                                    <div class="col-lg-3 col-sm-6">
                                        <div>
                                            <p class="mb-2 text-uppercase fw-medium fs-13">Create Date :</p>
                                            <h5 class="fs-15 mb-0">{{$data->created_at}}</h5>
                                        </div>
                                    </div>

                                </div>
                            </div>
                            <div class="pt-3 pb-3 border-bottom border-bottom-dashed mt-4">
                                <div class="row">
                                    @if($data->tags && count($data->tags_array)>0)
                                    <div class="col-lg-3 col-sm-6">
                                        <div>
                                            <p class="mb-2 text-uppercase fw-medium fs-13">Tags :</p>
                                            @foreach($data->tags_array as $tag)
                                            <div class="badge bg-success fs-12">{{$tag}}</div>
                                            @endforeach
                                        </div>
                                    </div>
                                    @endif
                                    @if($data->topics && count($data->topics_array)>0)
                                    <div class="col-lg-3 col-sm-6">
                                        <div>
                                            <p class="mb-2 text-uppercase fw-medium fs-13">Topics :</p>
                                            @foreach($data->topics_array as $topic)
                                            <div class="badge bg-success fs-12">{{$topic}}</div>
                                            @endforeach
                                        </div>
                                    </div>
                                    @endif
                                </div>
                            </div>
                            @if($data->description_unformatted)
                            <div class="pt-3 pb-3 border-bottom border-bottom-dashed mt-4">
                                <h6 class="fw-semibold text-uppercase">Description</h6>
                                <p>{!!$data->description!!}</p>
                            </div>
                            @endif

                            @if($data->document_link)
                            <div>
                                <iframe src="{{route('document_reader', ['uuid' => $data->uuid])}}" class="w-100 iframe-h" frameborder="0"></iframe>
                            </div>
                            @endif


                        </div>
                    </div>
                    <!-- end card body -->
                </div>
                <!-- end card -->
            </div>
        </div>


    </div> <!-- container-fluid -->
</div><!-- End Page-content -->



@stop

@section('javascript')
@include('includes.admin.delete_handler')

@stop
