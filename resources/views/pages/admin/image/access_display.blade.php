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
            'page_name' => "Access Request",
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
                                    @if($data->User->user_type == 2)
                                    <a href="{{route('subadmin_makeUserPreviledge', $data->User->id)}}" type="button" class="btn btn-warning add-btn me-2" id="create-btn"> Grant Access To All Files</a>
                                    @elseif($data->User->user_type == 3)
                                    <a href="{{route('subadmin_makeUserPreviledge', $data->User->id)}}" type="button" class="btn btn-warning add-btn me-2" id="create-btn"> Revoke Access To All Files</a>
                                    @endif
                                    @if($data->User->user_type == 2)
                                    <div class="edit">
                                        <form action="{{route('image_toggle_access', $data->id)}}" method="post">
                                            @csrf
                                            <input type="hidden" name="status" value="{{$data->status == 1 ? 0 : 1}}">
                                            <button type="submit" class="btn btn-success add-btn me-2">
                                                @if($data->status == 1)
                                                Revoke Access
                                                @else
                                                Grant Access
                                                @endif
                                            </button>
                                        </form>
                                    </div>
                                    @endif
                                    <button type="button" class="btn btn-danger add-btn remove-item-btn" data-link="{{route('image_delete_access', $data->id)}}" id="create-btn"><i class="ri-delete-bin-line align-bottom me-1 pointer-events-none"></i> Delete</button>
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
                                            <p class="mb-2 text-uppercase fw-medium fs-13">Accessible :</p>
                                            @if($data->User->user_type == 2)
                                            @if($data->status == 1)
                                            <div class="badge bg-success fs-12">Yes</div>
                                            @else
                                            <div class="badge bg-danger fs-12">No</div>
                                            @endif
                                            @else
                                            <div class="badge bg-success fs-12">Yes</div>
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
@include('includes.admin.image_previewer_script')
@stop
