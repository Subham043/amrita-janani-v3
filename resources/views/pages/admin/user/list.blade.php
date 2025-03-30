@extends('layouts.admin.dashboard')



@section('content')

    <div class="page-content">
        <div class="container-fluid">

            @include('includes.admin.page_title', [
                'page_name' => 'User',
                'current_page' => 'List',
            ])

            <div class="row">
                <div class="col-lg-12">
                    <div class="card">
                        <div class="card-header d-flex align-items-center justify-content-between">
                            <h4 class="card-title mb-0">User</h4>
                            <div class="flex-shrink-0 d-none" id="user_multiple_action_container">
                                <button id="block_multiple_users" type="button" class="btn btn-warning">Block</button>
                                <button id="unblock_multiple_users" type="button" class="btn btn-secondary">Unblock</button>
                                <button id="remove_multiple_users" type="button" class="btn btn-danger">Delete</button>

                            </div>
                        </div><!-- end card header -->

                        <div class="card-body">
                            <div id="customerList">
                                <div class="row g-4 mb-3 justify-content-between">
                                    <div class="col-sm-auto">
                                        <div>
                                            <a href="{{ route('subadmin_create') }}" type="button"
                                                class="btn btn-success add-btn" id="create-btn"><i
                                                    class="ri-add-line align-bottom me-1"></i> Create</a>
                                            <a href="{{ route('subadmin_excel') }}?{{ request()->getQueryString() }}" type="button"
                                                class="btn btn-info add-btn" id="create-btn"><i
                                                    class="ri-file-excel-fill align-bottom me-1"></i> Excel</a>
                                            <a href="{{ route('subadmin_email') }}" type="button"
                                                class="btn btn-dark add-btn" id="create-btn"><i
                                                    class="ri-mail-send-line align-bottom me-1"></i> Test Email</a>
                                        </div>
                                    </div>
                                    <div class="col-sm ">
                                        <form  method="get" action="{{route('subadmin_view')}}" class="col-sm-auto d-flex gap-2 justify-content-end">
                                            <div class="d-flex justify-content-sm-end">
                                                <div class="search-box ms-2">
                                                    <select name="filter[status]" id="filter" class="form-control search-handler">
                                                        <option value="all" @if(($filter_status=='all')) selected @endif>All</option>
                                                        <option value="1" @if(($filter_status=='1')) selected @endif>Active</option>
                                                        <option value="2" @if(($filter_status=='2')) selected @endif>Blocked</option>
                                                    </select>
                                                    <i class="ri-arrow-up-down-line search-icon"></i>
                                                </div>
                                            </div>
                                            <div class="d-flex justify-content-sm-end">
                                                <div class="search-box ms-2">
                                                    <select name="filter[verification]" id="filter" class="form-control search-handler">
                                                        <option value="all" @if(($filter_verification=='all')) selected @endif>All</option>
                                                        <option value="yes" @if(($filter_verification=='yes')) selected @endif>Verified</option>
                                                        <option value="no" @if(($filter_verification=='no')) selected @endif>Not Verified</option>
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
                                    @if ($data->total() > 0)
                                        <table class="table align-middle table-nowrap" id="customerTable">
                                            <thead class="table-light">
                                                <tr>
                                                    <th class="sort" data-sort="customer_name">
                                                        <input type="checkbox" class="form-check-input" id="checkAll"
                                                            data-bs-toggle="tooltip" data-bs-original-title="Select All">
                                                    </th>
                                                    <th class="sort" data-sort="customer_name">Name</th>
                                                    <th class="sort" data-sort="customer_name">Email</th>
                                                    <th class="sort" data-sort="customer_name">Phone</th>
                                                    <th class="sort" data-sort="customer_name">Role</th>
                                                    <th class="sort" data-sort="status">Status</th>
                                                    <th class="sort" data-sort="status">Verification Status</th>
                                                    <th class="sort" data-sort="date">Created Date</th>
                                                    <th class="sort" data-sort="action">Action</th>
                                                </tr>
                                            </thead>
                                            <tbody class="list form-check-all">

                                                @foreach ($data->items() as $item)
                                                    <tr>
                                                        <td class="customer_name">
                                                            <input type="checkbox" class="form-check-input user-checkbox"
                                                                value="{{ $item->id }}" data-bs-toggle="tooltip"
                                                                data-bs-original-title="Select User#{{ $item->id }}">
                                                        </td>
                                                        <td class="customer_name">{{ $item->name }}</td>
                                                        <td class="customer_name">{{ $item->email }}</td>
                                                        <td class="customer_name">{{ $item->phone }}</td>
                                                        <td class="customer_name">{{ $item->role }}</td>
                                                        @if ($item->status == 1)
                                                            <td class="status"><span
                                                                    class="badge badge-soft-success text-uppercase">Active</span>
                                                            </td>
                                                        @else
                                                            <td class="status"><span
                                                                    class="badge badge-soft-danger text-uppercase">Blocked</span>
                                                            </td>
                                                        @endif
                                                        @if ($item->email_verified_at)
                                                            <td class="status"><span
                                                                    class="badge badge-soft-success text-uppercase">Yes</span>
                                                            </td>
                                                        @else
                                                            <td class="status"><span
                                                                    class="badge badge-soft-danger text-uppercase">No</span>
                                                            </td>
                                                        @endif
                                                        <td class="date">{{ $item->created_at }}</td>
                                                        <td>
                                                            <div class="d-flex gap-2">
                                                                <!-- <div class="edit">
                                                                <a href="{{ route('subadmin_display', $item->id) }}" class="btn btn-sm btn-info edit-item-btn">View</a>
                                                            </div> -->
                                                                <div class="edit">
                                                                    <a href="{{ route('subadmin_edit', $item->id) }}"
                                                                        class="btn btn-sm btn-success edit-item-btn">Edit</a>
                                                                </div>
                                                                @if ($item->user_type != 1)
                                                                    <div class="edit">
                                                                        <a href="{{ route('subadmin_toggleUserStatus', $item->id) }}"
                                                                            class="btn btn-sm btn-info edit-item-btn">{{ $item->status == 1 ? 'Block' : 'Unblock' }}</a>
                                                                    </div>
                                                                @endif
                                                                <div class="remove">
                                                                    <button class="btn btn-sm btn-danger remove-item-btn"
                                                                        data-link="{{ route('subadmin_delete', $item->id) }}">Delete</button>
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

                                {{ $data->onEachSide(5)->links('includes.admin.pagination') }}
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
        let user_arr = []
        const checkAll = document.getElementById('checkAll');
        if(checkAll){
            checkAll.addEventListener('input', function() {
                const user_checkbox = document.querySelectorAll('.user-checkbox');
                if (checkAll.checked) {
                    for (let index = 0; index < user_checkbox.length; index++) {
                        if (user_checkbox[index].value.length > 0) {
                            user_checkbox[index].checked = true
                            if (!user_arr.includes(user_checkbox[index].value)) {
                                user_arr.push(user_checkbox[index].value);
                            }
                        }
                    }
                } else {
                    for (let index = 0; index < user_checkbox.length; index++) {
                        if (user_checkbox[index].value.length > 0) {
                            user_checkbox[index].checked = false
                            user_arr = [];
                        }
                    }
                }
                toggleMultipleActionBtn()
            })
        }


        document.querySelectorAll('.user-checkbox').forEach(el => {
            el.addEventListener('input', function(event) {
                toggleSingleActionBtn(event)
            })
        });

        const toggleMultipleActionBtn = () => {
            document.querySelectorAll('.user-checkbox').forEach(el => {
                if (el.checked && user_arr.length > 0) {
                    document.getElementById('user_multiple_action_container').classList.add('d-inline-block')
                    document.getElementById('user_multiple_action_container').classList.remove('d-none')
                } else {
                    document.getElementById('user_multiple_action_container').classList.add('d-none')
                    document.getElementById('user_multiple_action_container').classList.remove('d-inline-block')
                }
            })
        }

        const toggleSingleActionBtn = (event) => {
            if (!event.target.checked) {
                user_arr = user_arr.filter(function(item) {
                    return item !== event.target.value
                })
            } else {
                if (!user_arr.includes(event.target.value)) {
                    user_arr.push(event.target.value)
                }
            }
            if (!event.target.checked && user_arr.length < 1) {
                document.getElementById('user_multiple_action_container').classList.add('d-none')
                document.getElementById('user_multiple_action_container').classList.remove('d-inline-block')
            } else {
                document.getElementById('user_multiple_action_container').classList.add('d-inline-block')
                document.getElementById('user_multiple_action_container').classList.remove('d-none')
            }
        }


        document.getElementById('block_multiple_users').addEventListener('click', function() {
            block_multiple_action_handler()
        })
        
        document.getElementById('unblock_multiple_users').addEventListener('click', function() {
            unblock_multiple_action_handler()
        })
        
        document.getElementById('remove_multiple_users').addEventListener('click', function() {
            remove_multiple_action_handler()
        })

        const block_multiple_action_handler = () => {
            iziToast.question({
                timeout: false,
                close: false,
                overlay: true,
                displayMode: 'once',
                id: 'question',
                zindex: 999,
                title: 'Hey',
                message: 'Are you sure about blocking the selected users?',
                position: 'center',
                buttons: [
                    ['<button><b>YES</b></button>', async function(instance, toast) {

                        instance.hide({
                            transitionOut: 'fadeOut'
                        }, toast, 'button');
                        var submitBtn = document.getElementById('block_multiple_users');
                        submitBtn.innerHTML = `Blocking ...`
                        submitBtn.disabled = true;
                        try {

                            const response = await axios.post(
                                '{{ route('subadmin_multi_status') }}?status=2', {
                                    users: user_arr
                                })
                            successToast(response.data.message)
                            setInterval(window.location.replace("{{ route('subadmin_view') }}"),1500);
                        } catch (error) {
                            console.log(error)
                            if (error?.response?.data?.message) {
                                errorToast(error?.response?.data?.message)
                            }
                        } finally {
                            submitBtn.innerHTML = `Block`
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
        
        const unblock_multiple_action_handler = () => {
            iziToast.question({
                timeout: false,
                close: false,
                overlay: true,
                displayMode: 'once',
                id: 'question',
                zindex: 999,
                title: 'Hey',
                message: 'Are you sure about unblocking the selected users?',
                position: 'center',
                buttons: [
                    ['<button><b>YES</b></button>', async function(instance, toast) {

                        instance.hide({
                            transitionOut: 'fadeOut'
                        }, toast, 'button');
                        var submitBtn = document.getElementById('unblock_multiple_users');
                        submitBtn.innerHTML = `Unblocking ...`
                        submitBtn.disabled = true;
                        try {

                            const response = await axios.post(
                                '{{ route('subadmin_multi_status') }}?status=1', {
                                    users: user_arr
                                })
                            successToast(response.data.message)
                            setInterval(window.location.replace("{{ route('subadmin_view') }}"),1500);
                        } catch (error) {
                            console.log(error)
                            if (error?.response?.data?.message) {
                                errorToast(error?.response?.data?.message)
                            }
                        } finally {
                            submitBtn.innerHTML = `Unblock`
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
                message: 'Are you sure about deleting the selected users?',
                position: 'center',
                buttons: [
                    ['<button><b>YES</b></button>', async function(instance, toast) {

                        instance.hide({
                            transitionOut: 'fadeOut'
                        }, toast, 'button');
                        var submitBtn = document.getElementById('remove_multiple_users');
                        submitBtn.innerHTML = `Deleting ...`
                        submitBtn.disabled = true;
                        try {

                            const response = await axios.post(
                                '{{ route('subadmin_multi_delete') }}', {
                                    users: user_arr
                                })
                            successToast(response.data.message)
                            setInterval(window.location.replace("{{ route('subadmin_view') }}"),1500);
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
