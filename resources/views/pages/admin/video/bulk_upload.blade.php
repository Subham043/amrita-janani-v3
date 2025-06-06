@extends('layouts.admin.dashboard')


@section('css')
<link rel="stylesheet" href="{{ asset('admin/libs/filepond/filepond.min.css')}}" type="text/css" />
<link rel="stylesheet" href="{{ asset('admin/libs/filepond-plugin-image-preview/filepond-plugin-image-preview.min.css')}}">
@stop


@section('content')

<div class="page-content">
    <div class="container-fluid">

        @include('includes.admin.page_title', [
            'page_name' => "Videos",
            'current_page' => "Bulk Upload",
        ])

        <div class="row">
            <div class="col-lg-12">
                <div class="card">
                    <div class="card-header align-items-center d-flex">
                        <h4 class="card-title mb-0 flex-grow-1">Vedios</h4>
                        <div class="flex-shrink-0">
                            <div class="form-check form-switch form-switch-right form-switch-md">
                                <button type="button" class="btn rounded-pill btn-secondary waves-effect" data-bs-toggle="modal" data-bs-target="#myModal">Guide</button>
                            </div>
                        </div>
                    </div><!-- end card header -->
                    <div class="card-body">
                        <div class="live-preview">
                            <form id="countryForm" method="post" action="{{route('video_bulk_upload_store')}}" enctype="multipart/form-data">
                            @csrf
                            <div class="row gy-4">
                                <div class="col-xxl-12 col-md-12">
                                    <div>
                                        <label for="excel" class="form-label">Excel File</label>
                                        <input class="form-control" type="file" name="excel" id="excel">
                                        @error('excel')
                                            <div class="invalid-message">{{ $message }}</div>
                                        @enderror
                                    </div>
                                </div>

                                <div class="col-xxl-12 col-md-12">
                                    <button type="submit" class="btn btn-primary waves-effect waves-light" id="submitBtn">Upload</button>
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

@include('pages.admin.video.tutorial_modal')

@stop


@section('javascript')
<script src="{{ asset('admin/js/pages/axios.min.js') }}"></script>

<script type="text/javascript" nonce="{{ csp_nonce() }}">


// initialize the validation library
const validation = new JustValidate('#countryForm', {
      errorFieldCssClass: 'is-invalid',
});
// apply rules to form fields
validation
.addField('#excel', [
    {
        rule: 'minFilesCount',
        value: 1,
        errorMessage: 'Please upload an excel',
    },
    {
        rule: 'files',
        value: {
            files: {
                extensions: ['xls', 'xlsx']
            },
        },
        errorMessage: 'Please upload a valid excel',
    },
  ])
  .onSuccess(async (event) => {
    // event.target.submit();


        var submitBtn = document.getElementById('submitBtn')
        submitBtn.innerHTML = `
            <span class="d-flex align-items-center">
                <span class="spinner-border flex-shrink-0" role="status">
                    <span class="visually-hidden">Loading...</span>
                </span>
                <span class="flex-grow-1 ms-2">
                    Loading...
                </span>
            </span>
            `
        submitBtn.disabled = true;

      try {
        var formData = new FormData();
        formData.append('excel',document.getElementById('excel').files[0])
        // formData.append('refreshUrl','{{URL::current()}}')

        const response = await axios.post('{{route('video_bulk_upload_store')}}', formData)
        successToast(response.data.message)
        setTimeout(function(){
            window.location.replace(response.data.url);
        }, 1000);
      } catch (error) {
          console.log(error);
        if(error?.response?.data?.message){
            errorToast(error?.response?.data?.message)
        }
        if(error?.response?.data?.errors?.excel){
            validation.showErrors({'#excel': error?.response?.data?.errors?.excel[0]})
        }
      } finally{
            submitBtn.innerHTML =  `
                Upload
                `
            submitBtn.disabled = false;
        }
  });
</script>

@stop
