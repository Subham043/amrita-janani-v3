@extends('layouts.admin.dashboard')



@section('content')

<div class="page-content">
    <div class="container-fluid">

        @include('includes.admin.page_title', [
            'page_name' => "Audios",
            'current_page' => "List",
        ])

        <div class="row">
            <div class="col-lg-12">
                <div class="card">
                    <div class="card-header d-flex align-items-center justify-content-between">
                        <h4 class="card-title mb-0">Audios</h4>
                        <div class="flex-shrink-0 d-none" id="audio_multiple_action_container">
                            <button id="active_multiple_audios" type="button" class="btn btn-warning">Active</button>
                            <button id="inactive_multiple_audios" type="button" class="btn btn-warning">Inactive</button>
                            <button id="restricted_multiple_audios" type="button" class="btn btn-secondary">Restricted</button>
                            <button id="unrestricted_multiple_audios" type="button" class="btn btn-secondary">Unrestricted</button>
                            <button id="remove_multiple_audios" type="button" class="btn btn-danger">Delete</button>

                        </div>
                    </div><!-- end card header -->

                    <div class="card-body">
                        <div id="customerList">
                            <div class="row g-4 mb-3">
                                <div class="col-sm-auto">
                                    <div>
                                        <a href="{{route('audio_create')}}" type="button" class="btn btn-success add-btn" id="create-btn"><i class="ri-add-line align-bottom me-1"></i> Create</a>
                                        <a href="{{route('audio_excel')}}?{{ request()->getQueryString() }}" download type="button" class="btn btn-info add-btn" id="create-btn"><i class="ri-file-excel-fill align-bottom me-1"></i> Excel</a>
                                        <a href="{{route('audio_bulk_upload')}}" type="button" class="btn btn-warning add-btn" id="create-btn"><i class="ri-upload-cloud-2-line align-bottom me-1"></i> Bulk Upload</a>
                                        <a href="{{route('audio_view_trash')}}" type="button" class="btn btn-dark add-btn" id="create-btn"><i class="ri-delete-bin-line align-bottom me-1"></i> Recycle Bin</a>
                                    </div>
                                </div>
                                <div class="col-sm">
                                    <form  method="get" action="{{route('audio_view')}}" class="col-sm-auto d-flex gap-2 justify-content-end">
                                        <div class="d-flex justify-content-sm-end">
                                            <div class="search-box ms-2">
                                                <select name="filter[status]" id="filter" class="form-control search-handler">
                                                    <option value="all" @if(($filter_status=='all')) selected @endif>All</option>
                                                    <option value="1" @if(($filter_status=='1')) selected @endif>Active</option>
                                                    <option value="0" @if(($filter_status=='0')) selected @endif>Inactive</option>
                                                </select>
                                                <i class="ri-arrow-up-down-line search-icon"></i>
                                            </div>
                                        </div>
                                        <div class="d-flex justify-content-sm-end">
                                            <div class="search-box ms-2">
                                                <select name="filter[restricted]" id="filter" class="form-control search-handler">
                                                    <option value="all" @if(($filter_restricted=='all')) selected @endif>All</option>
                                                    <option value="1" @if(($filter_restricted=='1')) selected @endif>Restriced</option>
                                                    <option value="0" @if(($filter_restricted=='0')) selected @endif>Not Restriced</option>
                                                </select>
                                                <i class="ri-arrow-up-down-line search-icon"></i>
                                            </div>
                                        </div>
                                        <div class="d-flex justify-content-sm-end">
                                            <div class="search-box ms-2">
                                                <input type="text" name="filter[search]" class="form-control search" placeholder="Search..." value="{{request()->query('filter')['search'] ?? ''}}">
                                                <i class="ri-search-line search-icon"></i>
                                            </div>
                                        </div>
                                        <button type="submit"
                                            class="btn btn-dark add-btn">Filter</button>
                                    </form>
                                </div>
                            </div>
                            <div class="table-responsive table-card mt-3 mb-1">
                                @if($data->total() > 0)
                                <table class="table align-middle table-nowrap" id="customerTable">
                                    <thead class="table-light">
                                        <tr>
                                            <th class="sort" data-sort="customer_name">
                                                <input type="checkbox" class="form-check-input" id="checkAll"
                                                    data-bs-toggle="tooltip" data-bs-original-title="Select All">
                                            </th>
                                            <th class="sort" data-sort="customer_name">Title</th>
                                            <th class="sort" data-sort="customer_name">Langauges</th>
                                            <th class="sort" data-sort="customer_name">UUID</th>
                                            <th class="sort" data-sort="status">Status</th>
                                            <th class="sort" data-sort="status">Restricted</th>
                                            <th class="sort" data-sort="date">Created Date</th>
                                            <th class="sort" data-sort="action">Action</th>
                                            </tr>
                                    </thead>
                                    <tbody class="list form-check-all">

                                        @foreach ($data->items() as $item)
                                        <tr>
                                            <td class="customer_name">
                                                <input type="checkbox" class="form-check-input audio-checkbox"
                                                    value="{{ $item->id }}" data-bs-toggle="tooltip"
                                                    data-bs-original-title="Select Audio#{{ $item->id }}">
                                            </td>
                                            <td class="customer_name">{{$item->title}}</td>
                                            <td class="customer_name">
                                                @if($item->languages->count()>0)
                                                @foreach ($item->languages as $languages)
                                                    <div class="badge bg-secondary fs-12">{{$languages->name}}</div>
                                                @endforeach
                                                @endif
                                            </td>
                                            <td class="customer_name">{{$item->uuid}}</td>
                                            @if($item->status == 1)
                                            <td class="status"><span class="badge badge-soft-success text-uppercase">Active</span></td>
                                            @else
                                            <td class="status"><span class="badge badge-soft-danger text-uppercase">Inactive</span></td>
                                            @endif
                                            @if($item->restricted == 1)
                                            <td class="status"><span class="badge badge-soft-success text-uppercase">Yes</span></td>
                                            @else
                                            <td class="status"><span class="badge badge-soft-danger text-uppercase">No</span></td>
                                            @endif
                                            <td class="date">{{$item->created_at}}</td>
                                            <td>
                                                <div class="d-flex gap-2">
                                                    <div class="edit">
                                                        <a href="{{route('audio_display', $item->id)}}" class="btn btn-sm btn-info edit-item-btn">View</a>
                                                    </div>
                                                    <div class="edit">
                                                        <a href="{{route('audio_edit', $item->id)}}" class="btn btn-sm btn-success edit-item-btn">Edit</a>
                                                    </div>
                                                    <div class="remove">
                                                        <button class="btn btn-sm btn-danger remove-item-btn" data-link="{{route('audio_delete', $item->id)}}" >Delete</button>
                                                    </div>
                                                </div>
                                            </td>
                                        </tr>
                                        @endforeach

                                    </tbody>
                                </table>
                                @else
                                    @include('includes.admin.no_result')
                                @endif
                            </div>

                            {{$data->onEachSide(5)->links('includes.admin.pagination')}}
                        </div>
                    </div><!-- end card -->
                </div>
                <!-- end col -->
            </div>
            <!-- end col -->
        </div>
        <!-- end row -->

    </div>
</div>

@stop

@section('javascript')

@include('includes.admin.delete_handler')

<script src="{{ asset('main/js/plugins/axios.min.js') }}"></script>

<script type="text/javascript" nonce="{{ csp_nonce() }}">
    let audio_arr = []
    const checkAll = document.getElementById('checkAll');
    if(checkAll){
        checkAll.addEventListener('input', function() {
            const audio_checkbox = document.querySelectorAll('.audio-checkbox');
            if (checkAll.checked) {
                for (let index = 0; index < audio_checkbox.length; index++) {
                    if (audio_checkbox[index].value.length > 0) {
                        audio_checkbox[index].checked = true
                        if (!audio_arr.includes(audio_checkbox[index].value)) {
                            audio_arr.push(audio_checkbox[index].value);
                        }
                    }
                }
            } else {
                for (let index = 0; index < audio_checkbox.length; index++) {
                    if (audio_checkbox[index].value.length > 0) {
                        audio_checkbox[index].checked = false
                        audio_arr = [];
                    }
                }
            }
            toggleMultipleActionBtn()
        })
    }


    document.querySelectorAll('.audio-checkbox').forEach(el => {
        el.addEventListener('input', function(event) {
            toggleSingleActionBtn(event)
        })
    });

    const toggleMultipleActionBtn = () => {
        document.querySelectorAll('.audio-checkbox').forEach(el => {
            if (el.checked && audio_arr.length > 0) {
                document.getElementById('audio_multiple_action_container').classList.add('d-inline-block')
                document.getElementById('audio_multiple_action_container').classList.remove('d-none')
            } else {
                document.getElementById('audio_multiple_action_container').classList.add('d-none')
                document.getElementById('audio_multiple_action_container').classList.remove('d-inline-block')
            }
        })
    }

    const toggleSingleActionBtn = (event) => {
        if (!event.target.checked) {
            audio_arr = audio_arr.filter(function(item) {
                return item !== event.target.value
            })
        } else {
            if (!audio_arr.includes(event.target.value)) {
                audio_arr.push(event.target.value)
            }
        }
        if (!event.target.checked && audio_arr.length < 1) {
            document.getElementById('audio_multiple_action_container').classList.add('d-none')
            document.getElementById('audio_multiple_action_container').classList.remove('d-inline-block')
        } else {
            document.getElementById('audio_multiple_action_container').classList.add('d-inline-block')
            document.getElementById('audio_multiple_action_container').classList.remove('d-none')
        }
    }


    document.getElementById('active_multiple_audios').addEventListener('click', function() {
        active_multiple_action_handler(1)
    })
    
    document.getElementById('inactive_multiple_audios').addEventListener('click', function() {
        active_multiple_action_handler(0)
    })
    
    document.getElementById('restricted_multiple_audios').addEventListener('click', function() {
        restricted_multiple_action_handler(1)
    })
    
    document.getElementById('unrestricted_multiple_audios').addEventListener('click', function() {
        restricted_multiple_action_handler(0)
    })
    
    document.getElementById('remove_multiple_audios').addEventListener('click', function() {
        remove_multiple_action_handler()
    })

    const active_multiple_action_handler = (status = 1) => {
        iziToast.question({
            timeout: false,
            close: false,
            overlay: true,
            displayMode: 'once',
            id: 'question',
            zindex: 999,
            title: 'Hey',
            message: status ? 'Are you sure about making the selected audios active?' : 'Are you sure about making the selected audios inactive?',
            position: 'center',
            buttons: [
                ['<button><b>YES</b></button>', async function(instance, toast) {

                    instance.hide({
                        transitionOut: 'fadeOut'
                    }, toast, 'button');
                    var submitBtn = document.getElementById(status ? 'active_multiple_audios' : 'inactive_multiple_audios');
                    submitBtn.innerHTML = `Please Wait ...`
                    submitBtn.disabled = true;
                    try {

                        const response = await axios.post(
                            '{{ route('audio_multi_status') }}', {
                                audios: audio_arr,
                                status
                            })
                        successToast(response.data.message)
                        setInterval(window.location.replace("{{ route('audio_view') }}"),1500);
                    } catch (error) {
                        console.log(error)
                        if (error?.response?.data?.message) {
                            errorToast(error?.response?.data?.message)
                        }
                    } finally {
                        submitBtn.innerHTML = status ? `Active` : `Inactive`
                        submitBtn.disabled = false;
                    }

                }, true],
                ['<button>NO</button>', function(instance, toast) {

                    instance.hide({
                        transitionOut: 'fadeOut'
                    }, toast, 'button');

                }],
            ],
        });
    }
    
    const restricted_multiple_action_handler = (restricted = 0) => {
        iziToast.question({
            timeout: false,
            close: false,
            overlay: true,
            displayMode: 'once',
            id: 'question',
            zindex: 999,
            title: 'Hey',
            message: restricted ? 'Are you sure about restricting the selected audios?' : 'Are you sure about unrestricting the selected audios?',
            position: 'center',
            buttons: [
                ['<button><b>YES</b></button>', async function(instance, toast) {

                    instance.hide({
                        transitionOut: 'fadeOut'
                    }, toast, 'button');
                    var submitBtn = document.getElementById(restricted ? 'restricted_multiple_audios' : 'unrestricted_multiple_audios');
                    submitBtn.innerHTML = `Please Wait ...`
                    submitBtn.disabled = true;
                    try {

                        const response = await axios.post(
                            '{{ route('audio_multi_restriction') }}', {
                                audios: audio_arr,
                                restricted
                            })
                        successToast(response.data.message)
                        setInterval(window.location.replace("{{ route('audio_view') }}"),1500);
                    } catch (error) {
                        console.log(error)
                        if (error?.response?.data?.message) {
                            errorToast(error?.response?.data?.message)
                        }
                    } finally {
                        submitBtn.innerHTML = restricted ? `Restricted` : `Unrestricted`
                        submitBtn.disabled = false;
                    }

                }, true],
                ['<button>NO</button>', function(instance, toast) {

                    instance.hide({
                        transitionOut: 'fadeOut'
                    }, toast, 'button');

                }],
            ],
        });
    }
    
    const remove_multiple_action_handler = () => {
        iziToast.question({
            timeout: false,
            close: false,
            overlay: true,
            displayMode: 'once',
            id: 'question',
            zindex: 999,
            title: 'Hey',
            message: 'Are you sure about deleting the selected audios?',
            position: 'center',
            buttons: [
                ['<button><b>YES</b></button>', async function(instance, toast) {

                    instance.hide({
                        transitionOut: 'fadeOut'
                    }, toast, 'button');
                    var submitBtn = document.getElementById('remove_multiple_audios');
                    submitBtn.innerHTML = `Deleting ...`
                    submitBtn.disabled = true;
                    try {

                        const response = await axios.post(
                            '{{ route('audio_multi_delete') }}', {
                                audios: audio_arr
                            })
                        successToast(response.data.message)
                        setInterval(window.location.replace("{{ route('audio_view') }}"),1500);
                    } catch (error) {
                        console.log(error)
                        if (error?.response?.data?.message) {
                            errorToast(error?.response?.data?.message)
                        }
                    } finally {
                        submitBtn.innerHTML = `Delete`
                        submitBtn.disabled = false;
                    }

                }, true],
                ['<button>NO</button>', function(instance, toast) {

                    instance.hide({
                        transitionOut: 'fadeOut'
                    }, toast, 'button');

                }],
            ],
        });
    }
</script>

@stop
