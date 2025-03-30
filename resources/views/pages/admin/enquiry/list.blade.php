@extends('layouts.admin.dashboard')



@section('content')

<div class="page-content">
    <div class="container-fluid">

        @include('includes.admin.page_title', [
            'page_name' => "Enquiry",
            'current_page' => "List",
        ])

        <div class="row">
            <div class="col-lg-12">
                <div class="card">
                    <div class="card-header d-flex align-items-center justify-content-between">
                        <h4 class="card-title mb-0">Enquiries</h4>
                        <div class="flex-shrink-0 d-none" id="enquiry_multiple_action_container">
                            <button id="remove_multiple_enquiries" type="button" class="btn btn-danger">Delete</button>

                        </div>
                    </div><!-- end card header -->

                    <div class="card-body">
                        <div id="customerList">
                            <div class="row g-4 mb-3">
                                <div class="col-sm-auto">
                                    <div>
                                        <a href="{{route('enquiry_excel')}}?{{ request()->getQueryString() }}" type="button" class="btn btn-info add-btn" id="create-btn"><i class="ri-file-excel-fill align-bottom me-1"></i> Excel</a>
                                    </div>
                                </div>
                                <div class="col-sm">
                                    @include('includes.admin.common_search_form', [
                                        'url' => route('enquiry_view'),
                                    ])
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
                                            <th class="sort" data-sort="customer_name">Name</th>
                                            <th class="sort" data-sort="status">Email</th>
                                            <th class="sort" data-sort="customer_name">Phone</th>
                                            <th class="sort" data-sort="date">Created Date</th>
                                            <th class="sort" data-sort="action">Action</th>
                                            </tr>
                                    </thead>
                                    <tbody class="list form-check-all">

                                        @foreach ($data->items() as $item)
                                        <tr>
                                            <td class="customer_name">
                                                <input type="checkbox" class="form-check-input enquiry-checkbox"
                                                    value="{{ $item->id }}" data-bs-toggle="tooltip"
                                                    data-bs-original-title="Select Enquiry#{{ $item->id }}">
                                            </td>
                                            <td class="customer_name">{{$item->name}}</td>
                                            <td class="customer_name">{{$item->email}}</td>
                                            <td class="customer_name">{{$item->phone}}</td>
                                            <td class="date">{{$item->created_at}}</td>
                                            <td>
                                                <div class="d-flex gap-2">
                                                    <div class="edit">
                                                        <a href="{{route('enquiry_display', $item->id)}}" class="btn btn-sm btn-info edit-item-btn">View</a>
                                                    </div>
                                                    <div class="remove">
                                                        <button class="btn btn-sm btn-danger remove-item-btn" data-link="{{route('enquiry_delete', $item->id)}}">Delete</button>
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

{{-- <script src="{ asset('admin/libs/list.js/list.min.js') }}"></script> --}}
{{-- <script src="{ asset('admin/libs/list.pagination.js/list.pagination.min.js') }}"></script> --}}

<!-- listjs init -->
{{-- <script src="{ asset('admin/js/pages/listjs.init.js') }}"></script> --}}

@include('includes.admin.delete_handler')

<script src="{{ asset('main/js/plugins/axios.min.js') }}"></script>

<script type="text/javascript" nonce="{{ csp_nonce() }}">
    let enquiry_arr = []
    const checkAll = document.getElementById('checkAll');
    if(checkAll){
        checkAll.addEventListener('input', function() {
            const enquiry_checkbox = document.querySelectorAll('.enquiry-checkbox');
            if (checkAll.checked) {
                for (let index = 0; index < enquiry_checkbox.length; index++) {
                    if (enquiry_checkbox[index].value.length > 0) {
                        enquiry_checkbox[index].checked = true
                        if (!enquiry_arr.includes(enquiry_checkbox[index].value)) {
                            enquiry_arr.push(enquiry_checkbox[index].value);
                        }
                    }
                }
            } else {
                for (let index = 0; index < enquiry_checkbox.length; index++) {
                    if (enquiry_checkbox[index].value.length > 0) {
                        enquiry_checkbox[index].checked = false
                        enquiry_arr = [];
                    }
                }
            }
            toggleMultipleActionBtn()
        })
    }


    document.querySelectorAll('.enquiry-checkbox').forEach(el => {
        el.addEventListener('input', function(event) {
            toggleSingleActionBtn(event)
        })
    });

    const toggleMultipleActionBtn = () => {
        document.querySelectorAll('.enquiry-checkbox').forEach(el => {
            if (el.checked && enquiry_arr.length > 0) {
                document.getElementById('enquiry_multiple_action_container').classList.add('d-inline-block')
                document.getElementById('enquiry_multiple_action_container').classList.remove('d-none')
            } else {
                document.getElementById('enquiry_multiple_action_container').classList.add('d-none')
                document.getElementById('enquiry_multiple_action_container').classList.remove('d-inline-block')
            }
        })
    }

    const toggleSingleActionBtn = (event) => {
        if (!event.target.checked) {
            enquiry_arr = enquiry_arr.filter(function(item) {
                return item !== event.target.value
            })
        } else {
            if (!enquiry_arr.includes(event.target.value)) {
                enquiry_arr.push(event.target.value)
            }
        }
        if (!event.target.checked && enquiry_arr.length < 1) {
            document.getElementById('enquiry_multiple_action_container').classList.add('d-none')
            document.getElementById('enquiry_multiple_action_container').classList.remove('d-inline-block')
        } else {
            document.getElementById('enquiry_multiple_action_container').classList.add('d-inline-block')
            document.getElementById('enquiry_multiple_action_container').classList.remove('d-none')
        }
    }
    
    document.getElementById('remove_multiple_enquiries').addEventListener('click', function() {
        remove_multiple_action_handler()
    })
    
    const remove_multiple_action_handler = () => {
        iziToast.question({
            timeout: false,
            close: false,
            overlay: true,
            displayMode: 'once',
            id: 'question',
            zindex: 999,
            title: 'Hey',
            message: 'Are you sure about deleting the selected enquiries?',
            position: 'center',
            buttons: [
                ['<button><b>YES</b></button>', async function(instance, toast) {

                    instance.hide({
                        transitionOut: 'fadeOut'
                    }, toast, 'button');
                    var submitBtn = document.getElementById('remove_multiple_enquiries');
                    submitBtn.innerHTML = `Deleting ...`
                    submitBtn.disabled = true;
                    try {

                        const response = await axios.post(
                            '{{ route('enquiry_multi_delete') }}', {
                                enquiries: enquiry_arr
                            })
                        successToast(response.data.message)
                        setInterval(window.location.replace("{{ route('enquiry_view') }}"),1500);
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
