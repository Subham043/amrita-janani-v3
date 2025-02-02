@extends('layouts.admin.dashboard')


@section('content')

<div class="page-content">
    <div class="container-fluid">

        @include('includes.admin.page_title', [
            'page_name' => "Videos",
            'current_page' => "Edit",
        ])

        <div class="row">
        <div class="row g-4 mb-3">
                <div class="col-sm-auto">
                    <div>
                        <a href="{{url()->previous()}}" type="button" class="btn btn-dark add-btn" id="create-btn"><i class="ri-arrow-go-back-line align-bottom me-1"></i> Go Back</a>
                    </div>
                </div>
            </div>
            <div class="col-lg-12">
                <div class="card">
                    <div class="card-header align-items-center d-flex">
                        <h4 class="card-title mb-0 flex-grow-1">Videos</h4>
                    </div><!-- end card header -->
                    <div class="card-body">
                        <div class="live-preview">
                            <form id="countryForm" method="post" action="{{route('video_update', $country->id, $country->id)}}" enctype="multipart/form-data">
                            @csrf
                            <div class="row gy-4">
                                <div class="col-xxl-4 col-md-4">
                                    <div>
                                        <label for="title" class="form-label">Title</label>
                                        <input type="text" class="form-control" name="title" id="title" value="{{$country->title}}">
                                        @error('title')
                                            <div class="invalid-message">{{ $message }}</div>
                                        @enderror
                                    </div>
                                </div>
                                <div class="col-xxl-4 col-md-4">
                                    <div>
                                        <label for="year" class="form-label">Year</label>
                                        <input type="text" class="form-control" name="year" id="year" value="{{$country->year}}">
                                        @error('year')
                                            <div class="invalid-message">{{ $message }}</div>
                                        @enderror
                                    </div>
                                </div>
                                <div class="col-xxl-4 col-md-4">
                                    <div>
                                        <label for="version" class="form-label">Version</label>
                                        <input type="text" class="form-control" name="version" id="version" value="{{$country->version}}">
                                        @error('version')
                                            <div class="invalid-message">{{ $message }}</div>
                                        @enderror
                                    </div>
                                </div>
                                <div class="col-xxl-4 col-md-6">
                                    <div>
                                        <label for="deity" class="form-label">Deity</label>
                                        <input type="text" class="form-control" name="deity" id="deity" value="{{$country->deity}}">
                                        @error('deity')
                                            <div class="invalid-message">{{ $message }}</div>
                                        @enderror
                                    </div>
                                </div>
                                <div class="col-xxl-4 col-md-6">
                                    <div>
                                        <label for="tags" class="form-label">Tags</label>
                                        <input type="text" class="form-control" name="tags" id="tags" value="{{old('tags')}}">
                                        @error('tags')
                                            <div class="invalid-message">{{ $message }}</div>
                                        @enderror
                                    </div>
                                </div>
                                <div class="col-xxl-4 col-md-6">
                                    <div>
                                        <label for="topics" class="form-label">Topics</label>
                                        <input type="text" class="form-control" name="topics" id="topics" value="{{old('topics')}}">
                                        @error('topics')
                                            <div class="invalid-message">{{ $message }}</div>
                                        @enderror
                                    </div>
                                </div>
                                <div class="col-xxl-6 col-md-6">
                                    <div>
                                        <label for="language" class="form-label">Language</label>
                                        <select id="language" name="language" multiple></select>
                                        @error('language')
                                            <div class="invalid-message">{{ $message }}</div>
                                        @enderror
                                    </div>
                                </div>
                                <div class="col-xxl-6 col-md-6">
                                    <div>
                                        <label for="video" class="form-label">Video</label>
                                        <input type="text" class="form-control" name="video" id="video" value="{{$country->video}}">
                                        <div class="form-text"><code>youtube url format : </code>https://www.youtube.com/embed/3QPp_DlcZpM</div>
                                        <div class="form-text"><code>vimeo url format : </code>https://player.vimeo.com/video/291685166</div>
                                        @error('video')
                                            <div class="invalid-message">{{ $message }}</div>
                                        @enderror
                                    </div>
                                </div>

                                <div class="col-xxl-12 col-md-12">
                                    <div>
                                        <label for="description" class="form-label">Description</label>
                                        <div id="description">{!! $country->description !!}</div>
                                            @error('description')
                                                <div class="invalid-message">{{ $message }}</div>
                                            @enderror
                                    </div>
                                </div>
                                <!--end col-->
                                <div class="col-lg-12 col-md-12">
                                    <div class="mt-4 mt-md-0">
                                        <div>
                                            <div class="form-check form-switch form-check-right mb-2">
                                                <input class="form-check-input" type="checkbox" role="switch" id="flexSwitchCheckRightDisabled" name="status"  {{$country->status==1 ? 'checked' : ''}}>
                                                <label class="form-check-label" for="flexSwitchCheckRightDisabled">Status</label>
                                            </div>
                                        </div>

                                    </div>
                                </div><!--end col-->
                                <div class="col-lg-12 col-md-12">
                                    <div class="mt-4 mt-md-0">
                                        <div>
                                            <div class="form-check form-switch form-check-right mb-2">
                                                <input class="form-check-input" type="checkbox" role="switch" id="flexSwitchCheckRightDisabled2" name="restricted" {{$country->restricted==1 ? 'checked' : ''}}>
                                                <label class="form-check-label" for="flexSwitchCheckRightDisabled2">Restricted</label>
                                            </div>
                                        </div>

                                    </div>
                                </div><!--end col-->

                                <div class="col-xxl-12 col-md-12">
                                    <button type="submit" class="btn btn-primary waves-effect waves-light" id="submitBtn">Update</button>
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
<script src="{{ asset('admin/js/pages/axios.min.js') }}"></script>

@include('includes.admin.quill')
@include('includes.admin.tags_update_script')
@include('includes.admin.choice_language_update', ['languages' => $languages, 'country'=>$country])


<script type="text/javascript" nonce="{{ csp_nonce() }}">

// initialize the validation library
const validation = new JustValidate('#countryForm', {
      errorFieldCssClass: 'is-invalid',
});
// apply rules to form fields
validation
  .addField('#title', [
    {
      rule: 'required',
      errorMessage: 'Title is required',
    },
    {
        rule: 'customRegexp',
        value: /^[a-z 0-9~%.:_\@\-\/\(\)\\\#\;\[\]\{\}\$\!\&\<\>\'\r\n+=,]+$/i,
        errorMessage: 'Title is invalid',
    },
  ])
  .addField('#year', [
    {
        rule: 'customRegexp',
        value: /^[0-9]*$/,
        errorMessage: 'Year is invalid',
    },
  ])
  .addField('#version', [
    {
        rule: 'customRegexp',
        value: /^[a-z 0-9~%.:_\@\-\/\(\)\\\#\;\[\]\{\}\$\!\&\<\>\'\r\n+=,]+$/i,
        errorMessage: 'Version is invalid',
    },
  ])
  .addField('#deity', [
    {
        rule: 'customRegexp',
        value: /^[a-z 0-9~%.:_\@\-\/\(\)\\\#\;\[\]\{\}\$\!\&\<\>\'\r\n+=,]+$/i,
        errorMessage: 'Deity is invalid',
    },
  ])
  .addField('#language', [
    {
      rule: 'required',
      errorMessage: 'Please select a language',
    },
    {
        validator: (value, fields) => {
        if (value?.length==0) {
            return false;
        }

        return true;
        },
        errorMessage: 'Please select a language',
    },
  ])
  .addField('#video', [
    {
      rule: 'required',
      errorMessage: 'Video is required',
    }
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
        formData.append('title',document.getElementById('title').value)
        formData.append('year',document.getElementById('year').value)
        formData.append('deity',document.getElementById('deity').value)
        formData.append('version',document.getElementById('version').value)
        formData.append('description_unformatted',quillDescription.getText())
        formData.append('description',quillDescription.root.innerHTML)
        formData.append('status',document.getElementById('flexSwitchCheckRightDisabled').checked === true ? 'on' : 'off')
        formData.append('restricted',document.getElementById('flexSwitchCheckRightDisabled2').checked === true ? 'on' : 'off')
        formData.append('video',document.getElementById('video').value)
        if(tagify.value.length > 0){
            var tags = tagify.value.map(item => item.value).join(',')
            // console.log(tags);
            formData.append('tags',tags)
        }
        if(tagifyTopic.value.length > 0){
            var topicsData = tagifyTopic.value.map(item => item.value).join(',')
            // console.log(topicsData);
            formData.append('topics',topicsData)
        }
        if(document.getElementById('language')?.length>0){
            for (let index = 0; index < document.getElementById('language').length; index++) {
                formData.append('language[]',document.getElementById('language')[index].value)
            }
        }
        // formData.append('refreshUrl','{{URL::current()}}')

        const response = await axios.post('{{route('video_update', $country->id)}}', formData)
        successToast(response.data.message)
        setTimeout(function(){
            window.location.replace(response.data.url);
        }, 1000);
      } catch (error) {
        //   console.log(error.response);
        if(error?.response?.data?.errors?.title){
            errorToast(error?.response?.data?.errors?.title[0])
        }
        if(error?.response?.data?.errors?.year){
            errorToast(error?.response?.data?.errors?.year[0])
        }
        if(error?.response?.data?.errors?.deity){
            errorToast(error?.response?.data?.errors?.deity[0])
        }
        if(error?.response?.data?.errors?.version){
            errorToast(error?.response?.data?.errors?.version[0])
        }
        if(error?.response?.data?.errors?.language){
            errorToast(error?.response?.data?.errors?.language[0])
        }
        if(error?.response?.data?.errors?.description){
            errorToast(error?.response?.data?.errors?.description[0])
        }
        if(error?.response?.data?.errors?.language){
            errorToast(error?.response?.data?.errors?.language[0])
        }
        if(error?.response?.data?.errors?.video){
            errorToast(error?.response?.data?.errors?.video[0])
        }
      } finally{
            submitBtn.innerHTML =  `
                Update
                `
            submitBtn.disabled = false;
        }
  });
</script>

@stop
