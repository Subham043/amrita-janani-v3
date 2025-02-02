@extends('layouts.admin.dashboard')



@section('content')

<div class="page-content">
    <div class="container-fluid">

        @include('includes.admin.page_title', [
            'page_name' => "Access Request",
            'current_page' => "List",
        ])

        <div class="row">
            <div class="col-lg-12">
                <div class="card">
                    <div class="card-header">
                        <h4 class="card-title mb-0">Images</h4>
                    </div><!-- end card header -->

                    <div class="card-body">
                        <div id="customerList">
                            <div class="row g-4 mb-3">

                                <div class="col-sm row mt-4 justify-content-end">
                                    @include('includes.admin.filter_search_access_form', [
                                        'url' => route('image_view_access'),
                                    ])
                                </div>
                            </div>
                            <div class="table-responsive table-card mt-3 mb-1">
                                @if($country->total() > 0)
                                <table class="table align-middle table-nowrap" id="customerTable">
                                    <thead class="table-light">
                                        <tr>
                                            <th class="sort" data-sort="customer_name">Image Title</th>
                                            <th class="sort" data-sort="customer_name">Image UUID</th>
                                            <th class="sort" data-sort="customer_name">User Name</th>
                                            <th class="sort" data-sort="customer_name">User Email</th>
                                            <th class="sort" data-sort="status">Accessible</th>
                                            <th class="sort" data-sort="date">Requested Date</th>
                                            <th class="sort" data-sort="action">Action</th>
                                            </tr>
                                    </thead>
                                    <tbody class="list form-check-all">

                                        @foreach ($country->items() as $item)
                                        <tr>
                                            <td class="customer_name">{{$item->ImageModel->title}}</td>
                                            <td class="customer_name">{{$item->ImageModel->uuid}}</td>
                                            <td class="customer_name">{{$item->User->name}}</td>
                                            <td class="customer_name">{{$item->User->email}}</td>
                                            @if($item->User->userType == 2)
                                            @if($item->status == 1)
                                            <td class="status"><span class="badge badge-soft-success text-uppercase">Yes</span></td>
                                            @else
                                            <td class="status"><span class="badge badge-soft-danger text-uppercase">No</span></td>
                                            @endif
                                            @else
                                            <td class="status"><span class="badge badge-soft-success text-uppercase">Yes</span></td>
                                            @endif
                                            <td class="date">{{$item->created_at}}</td>
                                            <td>
                                                <div class="d-flex gap-2">
                                                    <div class="edit">
                                                        <a href="{{route('image_display_access', $item->id)}}" class="btn btn-sm btn-info edit-item-btn">View</a>
                                                    </div>
                                                    @if($item->User->userType == 2)
                                                    <div class="edit">
                                                        <a href="{{route('subadmin_makeUserPreviledge', $item->User->id)}}" class="btn btn-sm btn-dark edit-item-btn">Grant Access To All Files</a>
                                                    </div>
                                                    @elseif($item->User->userType == 3)
                                                    <div class="edit">
                                                        <a href="{{route('subadmin_makeUserPreviledge', $item->User->id)}}" class="btn btn-sm btn-dark edit-item-btn">Revoke Access To All Files</a>
                                                    </div>
                                                    @endif
                                                    @if($item->User->userType == 2)
                                                    @if($item->status == 1)
                                                    <div class="edit">
                                                        <a href="{{route('image_toggle_access', $item->id)}}" class="btn btn-sm btn-warning edit-item-btn">Revoke Access</a>
                                                    </div>
                                                    @else
                                                    <div class="edit">
                                                        <a href="{{route('image_toggle_access', $item->id)}}" class="btn btn-sm btn-warning edit-item-btn">Grant Access</a>
                                                    </div>
                                                    @endif
                                                    @endif
                                                    <div class="remove">
                                                        <button class="btn btn-sm btn-danger remove-item-btn" data-link="{{route('image_delete_access', $item->id)}}">Delete</button>
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

                            {{$country->onEachSide(5)->links('includes.admin.pagination')}}
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
@include('includes.admin.call_search_handler', ['url'=>route('image_view_access')])

@stop
