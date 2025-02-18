@extends('layouts.admin.dashboard')


@section('css')
<style nonce="{{ csp_nonce() }}">

    .max-width-30{
        max-width: 30%;
    }
</style>
@stop


@section('content')

<div class="page-content">
    <div class="container-fluid">

        @include('includes.admin.page_title', [
            'page_name' => "Reports",
            'current_page' => "View",
        ])

        <div class="row">
            <div class="col-lg-12">
                <div class="card">
                    <div class="card-body">
                        <div class="row g-4 mb-3">
                            <div class="col-sm-auto">
                                    <div>
                                        <a href="{{url()->previous()}}" type="button" class="btn btn-success add-btn" id="create-btn"><i class="ri-arrow-go-back-line"></i> Go Back</a>
                                    </div>
                            </div>
                            <div class="col-sm">
                                <div class="d-flex justify-content-sm-end gap-2">
                                    <form action="{{route('image_toggle_report', $data->id)}}" method="post" class="mr-3">
                                        @csrf
                                        <select class="form-control status-handler mr-2" name="status">
                                            <option value="0" {{ $data->status==0 ? 'selected':''}}>Pending</option>
                                            <option value="1" {{ $data->status==1 ? 'selected':''}}>In progress</option>
                                            <option value="2" {{ $data->status==2 ? 'selected':''}}>Completed</option>
                                        </select>
                                    </form>
                                    <button type="button" class="btn btn-danger add-btn remove-item-btn" data-link="{{route('image_delete_report', $data->id)}}" id="create-btn"><i class="ri-delete-bin-line align-bottom me-1 pointer-events-none"></i> Delete</button>
                                </div>
                            </div>
                        </div>
                        <div class="text-muted">
                            <div class="pt-3 pb-3 border-top border-top-dashed border-bottom border-bottom-dashed mt-4">
                                <div class="row">

                                    <div class="col-lg-3 col-sm-6">
                                        <div>
                                            <p class="mb-2 text-uppercase fw-medium fs-13">Image Title :</p>
                                            <h5 class="fs-15 mb-0"><a href="{{route('image_display', $data->ImageModel->id)}}" target="_blank" rel="noopener noreferrer">{{$data->ImageModel->title}}</a></h5>
                                        </div>
                                    </div>
                                    <div class="col-lg-3 col-sm-6">
                                        <div>
                                            <p class="mb-2 text-uppercase fw-medium fs-13">Image UUID :</p>
                                            <h5 class="fs-15 mb-0">{{$data->ImageModel->uuid}}</h5>
                                        </div>
                                    </div>
                                    <div class="col-lg-3 col-sm-6">
                                        <div>
                                            <p class="mb-2 text-uppercase fw-medium fs-13">User Name :</p>
                                            <h5 class="fs-15 mb-0"><a href="{{route('subadmin_display', $data->User->id)}}" target="_blank" rel="noopener noreferrer">{{$data->User->name}}</a></h5>
                                        </div>
                                    </div>
                                    <div class="col-lg-3 col-sm-6">
                                        <div>
                                            <p class="mb-2 text-uppercase fw-medium fs-13">User Email :</p>
                                            <h5 class="fs-15 mb-0">{{$data->User->email}}</h5>
                                        </div>
                                    </div>

                                </div>
                            </div>
                            <div class="pt-3 pb-3 border-bottom border-bottom-dashed mt-4">
                                <div class="row">

                                    <div class="col-lg-3 col-sm-6">
                                        <div>
                                            <p class="mb-2 text-uppercase fw-medium fs-13">Requested Date :</p>
                                            <h5 class="fs-15 mb-0">{{$data->created_at}}</h5>
                                        </div>
                                    </div>
                                    <div class="col-lg-3 col-sm-6">
                                        <div>
                                            <p class="mb-2 text-uppercase fw-medium fs-13">Status :</p>
                                            @if($data->status == 2)
                                            <div class="badge bg-success fs-12">Completed</div>
                                            @elseif($data->status == 1)
                                            <div class="badge bg-info fs-12">In Progress</div>
                                            @else
                                            <div class="badge bg-danger fs-12">Pending</div>
                                            @endif
                                        </div>
                                    </div>
                                </div>
                            </div>

                            @if($data->message)
                            <div class="pt-3 pb-3 border-bottom border-bottom-dashed mt-4">
                                <h6 class="fw-semibold text-uppercase">Message From {{$data->User->name}}</h6>
                                <p>{!!$data->message!!}</p>
                            </div>
                            @endif

                            <div id="image-container">
                                @if($data->ImageModel->image_link)
                                <div class="pt-3 pb-3 border-bottom border-bottom-dashed mt-4">
                                    <h6 class="fw-semibold text-uppercase">Image</h6>
                                    <img src="{!!$data->ImageModel->image_link!!}" class="mb-3 max-width-30">
                                </div>
                                @endif
                            </div>


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
@include('includes.admin.call_status_handler')
@include('includes.admin.image_previewer_script')
@stop
