@extends('layouts.admin.dashboard')



@section('content')

<div class="page-content">
    <div class="container-fluid">

        @include('includes.admin.page_title', [
            'page_name' => "Enquiry",
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
                                    <button type="button" class="btn btn-danger add-btn remove-item-btn" data-link="{{route('enquiry_delete', $data->id)}}" id="create-btn"><i class="ri-delete-bin-line align-bottom me-1 pointer-events-none"></i> Delete</button>
                                </div>
                            </div>
                        </div>
                        <div class="text-muted">
                            <div class="pt-3 pb-3 border-top border-top-dashed border-bottom border-bottom-dashed mt-4">
                                <div class="row">

                                    <div class="col-lg-3 col-sm-6">
                                        <div>
                                            <p class="mb-2 text-uppercase fw-medium fs-13">Name :</p>
                                            <h5 class="fs-15 mb-0">{{$data->name}} </h5>
                                        </div>
                                    </div>
                                    <div class="col-lg-3 col-sm-6">
                                        <div>
                                            <p class="mb-2 text-uppercase fw-medium fs-13">Email :</p>
                                            <h5 class="fs-15 mb-0">{{$data->email}}</h5>
                                        </div>
                                    </div>
                                    <div class="col-lg-3 col-sm-6">
                                        <div>
                                            <p class="mb-2 text-uppercase fw-medium fs-13">Phone :</p>
                                            <h5 class="fs-15 mb-0">{{$data->email}}</h5>
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
                            @if($data->system_info)
                            <h6 class="fw-semibold text-uppercase text-center pt-2">SYSTEM INFO</h6>
                            <div class="pb-3 border-top border-top-dashed border-bottom border-bottom-dashed">
                                <div class="row">
                                    @foreach($data->system_info as $system_info_key => $system_info_value)
                                    <div class="col-lg-3 col-sm-6 py-3">
                                        <div>
                                            <p class="mb-2 text-uppercase fw-medium fs-13">{{$system_info_key}} :</p>
                                            <h5 class="fs-15 mb-0">{{$system_info_value}} </h5>
                                        </div>
                                    </div>
                                    @endforeach
                                </div>
                            </div>
                            @endif
                            @if($data->message)
                            <div class="pt-3 pb-3 border-bottom border-bottom-dashed mt-4">
                                <h6 class="fw-semibold text-uppercase">Message</h6>
                                <p>{{$data->message}}</p>
                            </div>
                            @endif


                        </div>
                    </div>
                    <!-- end card body -->
                </div>
                <!-- end card -->
            </div>
        </div>

        <div class="row">
            <div class="col-lg-12">
                <div class="card">
                    <div class="card-header align-items-center d-flex">
                        <h4 class="card-title mb-0 flex-grow-1">Reply Via Mail</h4>
                    </div><!-- end card header -->
                    <div class="card-body">
                        <div class="live-preview">
                            <form id="contactForm" method="post" enctype="multipart/form-data">
                            @csrf
                            <div class="row gy-4">
                                <div class="col-xxl-12 col-md-12">
                                    <div>
                                        <label for="subject" class="form-label">Subject</label>
                                        <input type="text" class="form-control" name="subject" id="subject" value="{{old('subject')}}">
                                        @error('subject')
                                            <div class="invalid-message">{{ $message }}</div>
                                        @enderror
                                    </div>
                                </div>
                                <div class="col-xxl-12 col-md-12">
                                    <div>
                                        <label for="message" class="form-label">Message</label>
                                        <textarea class="form-control" name="message" id="message"></textarea>
                                        @error('message')
                                            <div class="invalid-message">{{ $message }}</div>
                                        @enderror
                                    </div>
                                </div>

                                <div class="col-xxl-12 col-md-12">
                                    <button type="submit" id="SubmitBtn" class="btn btn-primary waves-effect waves-light">Reply</button>
                                </div>

                            </div>
                            </form>
                            <!--end row-->
                        </div>

                    </div>
                </div>
            </div>
            <!--end col-->
        </div>
        <!--end row-->


    </div> <!-- container-fluid -->
</div><!-- End Page-content -->



@stop

@section('javascript')
<script src="{{ asset('admin/js/pages/just-validate.production.min.js') }}"></script>
<script src="{{ asset('main/js/plugins/axios.min.js') }}"></script>
@include('includes.admin.delete_handler')
<script type="text/javascript" nonce="{{ csp_nonce() }}">

const validationModal = new JustValidate('#contactForm', {
    errorFieldCssClass: 'is-invalid',
});

validationModal
.addField('#subject', [
{
    rule: 'required',
    errorMessage: 'Subject is required',
},
{
    rule: 'customRegexp',
    value: /^[a-z 0-9~%.:_\@\-\/\(\)\\\#\;\[\]\{\}\$\!\&\<\>\'\r\n+=,]+$/i,
    errorMessage: 'Subject is invalid',
},
])
.addField('#message', [
{
    rule: 'required',
    errorMessage: 'Message is required',
},
{
    rule: 'customRegexp',
    value: /^[a-z 0-9~%.:_\@\-\/\(\)\\\#\;\[\]\{\}\$\!\&\<\>\'\r\n+=,]+$/i,
    errorMessage: 'Message is invalid',
},
])
.onSuccess(async (event) => {
    event.target.preventDefault
    var submitBtn = document.getElementById('SubmitBtn')
    submitBtn.innerHTML = `
        <span class="d-flex align-items-center">
            <span class="spinner-border flex-shrink-0" role="status">
                <span class="visually-hidden"></span>
            </span>
            <span class="flex-grow-1 ms-2">
                &nbsp; Replying...
            </span>
        </span>
        `
    submitBtn.disabled = true;
    try {
        var formData = new FormData();
        formData.append('subject',document.getElementById('subject').value)
        formData.append('message',document.getElementById('message').value)
        const response = await axios.post('{{route('enquiry_reply', $data->id)}}', formData)
        successToast(response.data.message)
        event.target.reset()
        await reload_captcha()
    } catch (error) {
        if(error?.response?.data?.errors?.subject){
            validationModal.showErrors({
                '#subject': error?.response?.data?.errors?.subject[0]
            })
        }
        if(error?.response?.data?.errors?.message){
            validationModal.showErrors({
                '#message': error?.response?.data?.errors?.message[0]
            })
        }
        if(error?.response?.data?.error){
            errorToast(error?.response?.data?.error)
        }
    } finally{
        submitBtn.innerHTML =  `
            Reply
            `
        submitBtn.disabled = false;
    }
})

</script>
@stop
