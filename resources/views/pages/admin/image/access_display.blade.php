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
                                    @if($country->User->userType == 2)
                                    <a href="{{route('subadmin_makeUserPreviledge', $country->User->id)}}" type="button" class="btn btn-warning add-btn me-2" id="create-btn"> Grant Access To All Files</a>
                                    @elseif($country->User->userType == 3)
                                    <a href="{{route('subadmin_makeUserPreviledge', $country->User->id)}}" type="button" class="btn btn-warning add-btn me-2" id="create-btn"> Revoke Access To All Files</a>
                                    @endif
                                    @if($country->User->userType == 2)
                                    @if($country->status == 1)
                                    <a href="{{route('image_toggle_access', $country->id)}}" type="button" class="btn btn-success add-btn me-2" id="create-btn"> Revoke Access</a>
                                    @else
                                    <a href="{{route('image_toggle_access', $country->id)}}" type="button" class="btn btn-success add-btn me-2" id="create-btn"> Grant Access</a>
                                    @endif
                                    @endif
                                    <button type="button" class="btn btn-danger add-btn remove-item-btn" data-link="{{route('image_delete_access', $country->id)}}" id="create-btn"><i class="ri-delete-bin-line align-bottom me-1 pointer-events-none"></i> Delete</button>
                                </div>
                            </div>
                        </div>
                        <div class="text-muted">
                            <div class="pt-3 pb-3 border-top border-top-dashed border-bottom border-bottom-dashed mt-4">
                                <div class="row">

                                    <div class="col-lg-3 col-sm-6">
                                        <div>
                                            <p class="mb-2 text-uppercase fw-medium fs-13">Image Title :</p>
                                            <h5 class="fs-15 mb-0">{{$country->ImageModel->title}}</h5>
                                        </div>
                                    </div>
                                    <div class="col-lg-3 col-sm-6">
                                        <div>
                                            <p class="mb-2 text-uppercase fw-medium fs-13">Image UUID :</p>
                                            <h5 class="fs-15 mb-0">{{$country->ImageModel->uuid}}</h5>
                                        </div>
                                    </div>
                                    <div class="col-lg-3 col-sm-6">
                                        <div>
                                            <p class="mb-2 text-uppercase fw-medium fs-13">User Name :</p>
                                            <h5 class="fs-15 mb-0">{{$country->User->name}}</h5>
                                        </div>
                                    </div>
                                    <div class="col-lg-3 col-sm-6">
                                        <div>
                                            <p class="mb-2 text-uppercase fw-medium fs-13">User Email :</p>
                                            <h5 class="fs-15 mb-0">{{$country->User->email}}</h5>
                                        </div>
                                    </div>

                                </div>
                            </div>
                            <div class="pt-3 pb-3 border-bottom border-bottom-dashed mt-4">
                                <div class="row">

                                    <div class="col-lg-3 col-sm-6">
                                        <div>
                                            <p class="mb-2 text-uppercase fw-medium fs-13">Requested Date :</p>
                                            <h5 class="fs-15 mb-0">{{$country->created_at}}</h5>
                                        </div>
                                    </div>
                                    <div class="col-lg-3 col-sm-6">
                                        <div>
                                            <p class="mb-2 text-uppercase fw-medium fs-13">Accessible :</p>
                                            @if($country->User->userType == 2)
                                            @if($country->status == 1)
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

                            @if($country->message)
                            <div class="pt-3 pb-3 border-bottom border-bottom-dashed mt-4">
                                <h6 class="fw-semibold text-uppercase">Message From {{$country->User->name}}</h6>
                                <p>{!!$country->message!!}</p>
                            </div>
                            @endif

                            <div id="image-container">
                                @if($country->ImageModel->image)
                                <div class="pt-3 pb-3 border-bottom border-bottom-dashed mt-4">
                                    <h6 class="fw-semibold text-uppercase">Image</h6>
                                    <img src="{{asset('storage/upload/images/'.$country->ImageModel->image)}}" class="mb-3 max-width-30">
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
