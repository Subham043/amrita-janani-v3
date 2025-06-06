@extends('layouts.admin.dashboard')

@section('css')
<style nonce="{{ csp_nonce() }}">
    .max-width-30{
        max-width: 300px;
    }
</style>
@stop

@section('content')

<div class="page-content">
    <div class="container-fluid">

        @include('includes.admin.page_title', [
            'page_name' => "Images",
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
                        <h4 class="card-title mb-0 flex-grow-1">Images</h4>
                    </div><!-- end card header -->
                    <div class="card-body">
                        <div class="live-preview">
                            <form id="countryForm" method="post" action="{{route('image_update', $data->id)}}" enctype="multipart/form-data">
                            @csrf
                            <div class="row gy-4">
                                <div class="col-xxl-4 col-md-4">
                                    <div>
                                        <label for="title" class="form-label">Title</label>
                                        <input type="text" class="form-control" name="title" id="title" value="{{$data->title}}">
                                        @error('title')
                                            <div class="invalid-message">{{ $message }}</div>
                                        @enderror
                                    </div>
                                </div>
                                <div class="col-xxl-4 col-md-4">
                                    <div>
                                        <label for="year" class="form-label">Year</label>
                                        <input type="text" class="form-control" name="year" id="year" value="{{$data->year}}">
                                        @error('year')
                                            <div class="invalid-message">{{ $message }}</div>
                                        @enderror
                                    </div>
                                </div>
                                <div class="col-xxl-4 col-md-4">
                                    <div>
                                        <label for="version" class="form-label">Version</label>
                                        <input type="text" class="form-control" name="version" id="version" value="{{$data->version}}">
                                        @error('version')
                                            <div class="invalid-message">{{ $message }}</div>
                                        @enderror
                                    </div>
                                </div>
                                <div class="col-xxl-4 col-md-6">
                                    <div>
                                        <label for="deity" class="form-label">Deity</label>
                                        <input type="text" class="form-control" name="deity" id="deity" value="{{$data->deity}}">
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
                                <div class="col-xxl-12 col-md-12">
                                    <div>
                                        <label for="image" class="form-label">Image</label>
                                        <input class="form-control" type="file" name="image" id="image" accept="image/jpeg, image/png, image/jpg, image/webp">
                                        @error('image')
                                        <div class="invalid-message">{{ $message }}</div>
                                        @enderror
                                        <div id="image-container">
                                            @if($data->image_link)
                                            <img src="{{$data->image_link}}" class="mt-2 mb-2 max-width-30">
                                            @endif
                                        </div>
                                    </div>
                                </div>

                                <div class="col-xxl-12 col-md-12">
                                    <div>
                                        <label for="description" class="form-label">Description</label>
                                        <div id="description">{!! $data->description !!}</div>
                                            @error('description')
                                                <div class="invalid-message">{{ $message }}</div>
                                            @enderror
                                    </div>
                                </div>
                                <!--end col-->
                                <div class="col-xxl-6 col-md-6">
                                    <div>
                                        <label for="status" class="form-label">Status</label>
                                        <select id="status" name="status"></select>
                                        @error('status')
                                            <div class="invalid-message">{{ $message }}</div>
                                        @enderror
                                    </div>
                                </div>
                                <div class="col-xxl-6 col-md-6">
                                    <div>
                                        <label for="restricted" class="form-label">Restricted</label>
                                        <select id="restricted" name="restricted"></select>
                                        @error('restricted')
                                            <div class="invalid-message">{{ $message }}</div>
                                        @enderror
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
<script src="{{ asset('admin/js/pages/choices.min.js') }}"></script>

@include('includes.admin.quill')
@include('includes.admin.tags_update_script')

<script type="text/javascript" nonce="{{ csp_nonce() }}">
const restrictedChoices = new Choices('#restricted', {
    silent: false,
    items: [],
    choices: [
            {
                value: 'Select the restriction',
                label: 'Select the restriction',
                selected: false,
                disabled: true,
            },
        @foreach($restrictions as $key => $val)
            {
                value: '{{$val}}',
                label: '{{$key}}',
                selected: {{($data->restricted==$val) ? 'true' : 'false'}},
            },
        @endforeach
    ],
    renderChoiceLimit: -1,
    maxItemCount: -1,
    addItems: true,
    addItemFilter: null,
    removeItems: true,
    removeItemButton: false,
    editItems: false,
    allowHTML: true,
    duplicateItemsAllowed: true,
    delimiter: ',',
    paste: true,
    searchEnabled: true,
    searchChoices: true,
    searchFloor: 1,
    searchResultLimit: 4,
    searchFields: ['label', 'value'],
    position: 'auto',
    resetScrollPosition: true,
    shouldSort: true,
    shouldSortItems: false,
    // sorter: () => {...},
    placeholder: true,
    placeholderValue: 'Select the restriction',
    searchPlaceholderValue: null,
    prependValue: null,
    appendValue: null,
    renderSelectedChoices: 'auto',
    loadingText: 'Loading...',
    noResultsText: 'No results found',
    noChoicesText: 'No choices to choose from',
    itemSelectText: 'Press to select',
    addItemText: (value) => {
      return `Press Enter to add <b>"${value}"</b>`;
    },
    maxItemText: (maxItemCount) => {
      return `Only ${maxItemCount} values can be added`;
    },
    valueComparer: (value1, value2) => {
      return value1 === value2;
    },
    classNames: {
      containerOuter: 'choices',
      containerInner: 'choices__inner',
      input: 'choices__input',
      inputCloned: 'choices__input--cloned',
      list: 'choices__list',
      listItems: 'choices__list--multiple',
      listSingle: 'choices__list--single',
      listDropdown: 'choices__list--dropdown',
      item: 'choices__item',
      itemSelectable: 'choices__item--selectable',
      itemDisabled: 'choices__item--disabled',
      itemChoice: 'choices__item--choice',
      placeholder: 'choices__placeholder',
      group: 'choices__group',
      groupHeading: 'choices__heading',
      button: 'choices__button',
      activeState: 'is-active',
      focusState: 'is-focused',
      openState: 'is-open',
      disabledState: 'is-disabled',
      highlightedState: 'is-highlighted',
      selectedState: 'is-selected',
      flippedState: 'is-flipped',
      loadingState: 'is-loading',
      noResults: 'has-no-results',
      noChoices: 'has-no-choices'
    },
    // Choices uses the great Fuse library for searching. You
    // can find more options here: https://fusejs.io/api/options.html
    fuseOptions: {
      includeScore: true
    },
    labelId: '',
    callbackOnInit: null,
    callbackOnCreateTemplates: null
  });
const statusChoices = new Choices('#status', {
    silent: false,
    items: [],
    choices: [
            {
                value: 'Select the status',
                label: 'Select the status',
                selected: false,
                disabled: true,
            },
        @foreach($statuses as $key => $val)
            {
                value: '{{$val}}',
                label: '{{$key}}',
                selected: {{($data->status==$val) ? 'true' : 'false'}},
            },
        @endforeach
    ],
    renderChoiceLimit: -1,
    maxItemCount: -1,
    addItems: true,
    addItemFilter: null,
    removeItems: true,
    removeItemButton: false,
    editItems: false,
    allowHTML: true,
    duplicateItemsAllowed: true,
    delimiter: ',',
    paste: true,
    searchEnabled: true,
    searchChoices: true,
    searchFloor: 1,
    searchResultLimit: 4,
    searchFields: ['label', 'value'],
    position: 'auto',
    resetScrollPosition: true,
    shouldSort: true,
    shouldSortItems: false,
    // sorter: () => {...},
    placeholder: true,
    placeholderValue: 'Select the status',
    searchPlaceholderValue: null,
    prependValue: null,
    appendValue: null,
    renderSelectedChoices: 'auto',
    loadingText: 'Loading...',
    noResultsText: 'No results found',
    noChoicesText: 'No choices to choose from',
    itemSelectText: 'Press to select',
    addItemText: (value) => {
      return `Press Enter to add <b>"${value}"</b>`;
    },
    maxItemText: (maxItemCount) => {
      return `Only ${maxItemCount} values can be added`;
    },
    valueComparer: (value1, value2) => {
      return value1 === value2;
    },
    classNames: {
      containerOuter: 'choices',
      containerInner: 'choices__inner',
      input: 'choices__input',
      inputCloned: 'choices__input--cloned',
      list: 'choices__list',
      listItems: 'choices__list--multiple',
      listSingle: 'choices__list--single',
      listDropdown: 'choices__list--dropdown',
      item: 'choices__item',
      itemSelectable: 'choices__item--selectable',
      itemDisabled: 'choices__item--disabled',
      itemChoice: 'choices__item--choice',
      placeholder: 'choices__placeholder',
      group: 'choices__group',
      groupHeading: 'choices__heading',
      button: 'choices__button',
      activeState: 'is-active',
      focusState: 'is-focused',
      openState: 'is-open',
      disabledState: 'is-disabled',
      highlightedState: 'is-highlighted',
      selectedState: 'is-selected',
      flippedState: 'is-flipped',
      loadingState: 'is-loading',
      noResults: 'has-no-results',
      noChoices: 'has-no-choices'
    },
    // Choices uses the great Fuse library for searching. You
    // can find more options here: https://fusejs.io/api/options.html
    fuseOptions: {
      includeScore: true
    },
    labelId: '',
    callbackOnInit: null,
    callbackOnCreateTemplates: null
  });

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
  .addField('#image', [
    {
        rule: 'minFilesCount',
        value: 0,
        errorMessage: 'Please select an image',
    },
    {
        rule: 'files',
        value: {
            files: {
                extensions: ['jpeg', 'png', 'jpg', 'webp']
            },
        },
        errorMessage: 'Please select a valid image',
    },
  ])
  .addField('#restricted', [
    {
      rule: 'required',
      errorMessage: 'Please select the restriction',
    },
    {
        validator: (value, fields) => {
        if (value === 'Select the restriction') {
            return false;
        }
        return true;
        },
        errorMessage: 'Please select the restriction',
    },
  ])
  .addField('#status', [
    {
      rule: 'required',
      errorMessage: 'Please select the status',
    },
    {
        validator: (value, fields) => {
        if (value === 'Select the status') {
            return false;
        }
        return true;
        },
        errorMessage: 'Please select the status',
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
        formData.append('title',document.getElementById('title').value)
        formData.append('year',document.getElementById('year').value)
        formData.append('deity',document.getElementById('deity').value)
        formData.append('version',document.getElementById('version').value)
        formData.append('description_unformatted',quillDescription.getText())
        formData.append('description',quillDescription.root.innerHTML)
        formData.append('status',document.getElementById('status').value)
        formData.append('restricted',document.getElementById('restricted').value)
        if(document.getElementById('image').files.length > 0){
            formData.append('image',document.getElementById('image').files[0])
        }
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
        // formData.append('refreshUrl','{{URL::current()}}')

        const response = await axios.post('{{route('image_update', $data->id)}}', formData)
        successToast(response.data.message)
        setTimeout(function(){
            window.location.replace(response.data.url);
        }, 1000);
      } catch (error) {
        //   console.log(error.response);
        if(error?.response?.data?.errors?.title){
            validation.showErrors({'#title': error?.response?.data?.errors?.title[0]})
        }
        if(error?.response?.data?.errors?.year){
            validation.showErrors({'#year': error?.response?.data?.errors?.year[0]})
        }
        if(error?.response?.data?.errors?.deity){
            validation.showErrors({'#deity': error?.response?.data?.errors?.deity[0]})
        }
        if(error?.response?.data?.errors?.version){
            validation.showErrors({'#version': error?.response?.data?.errors?.version[0]})
        }
        if(error?.response?.data?.errors?.description){
            validation.showErrors({'#description': error?.response?.data?.errors?.description[0]})
        }
        if(error?.response?.data?.errors?.image){
            validation.showErrors({'#image': error?.response?.data?.errors?.image[0]})
        }
        if(error?.response?.data?.message){
            errorToast(error?.response?.data?.message)
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
