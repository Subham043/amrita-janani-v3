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
                        <h4 class="card-title mb-0">Videos</h4>
                    </div><!-- end card header -->

                    <div class="card-body">
                        <div id="customerList">
                            <div class="row g-4 mb-3">

                                <div class="col-sm row mt-4 justify-content-end">
                                    @include('includes.admin.filter_search_access_form', [
                                        'url' => route('video_view_access'),
                                    ])
                                </div>
                            </div>
                            <div class="table-responsive table-card mt-3 mb-1">
                                @if($data->total() > 0)
                                <table class="table align-middle table-nowrap" id="customerTable">
                                    <thead class="table-light">
                                        <tr>
                                            <th class="sort" data-sort="customer_name">Video Title</th>
                                            <th class="sort" data-sort="customer_name">Video UUID</th>
                                            <th class="sort" data-sort="customer_name">User Name</th>
                                            <th class="sort" data-sort="customer_name">User Email</th>
                                            <th class="sort" data-sort="status">Accessible</th>
                                            <th class="sort" data-sort="date">Requested Date</th>
                                            <th class="sort" data-sort="action">Action</th>
                                            </tr>
                                    </thead>
                                    <tbody class="list form-check-all">

                                        @foreach ($data->items() as $item)
                                        <tr>
                                            <td class="customer_name"><a href="{{route('video_display', $item->VideoModel->id)}}" target="_blank" rel="noopener noreferrer">{{$item->VideoModel->title}}</a></td>
                                            <td class="customer_name">{{$item->VideoModel->uuid}}</td>
                                            <td class="customer_name"><a href="{{route('subadmin_display', $item->User->id)}}" target="_blank" rel="noopener noreferrer">{{$item->User->name}}</a></td>
                                            <td class="customer_name">{{$item->User->email}}</td>
                                            @if($item->User->user_type == 2)
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
                                                        <a href="{{route('video_display_access', $item->id)}}" class="btn btn-sm btn-info edit-item-btn">View</a>
                                                    </div>
                                                    @if($item->User->user_type == 2)
                                                    <div class="edit">
                                                        <a href="{{route('subadmin_makeUserPreviledge', $item->User->id)}}" class="btn btn-sm btn-dark edit-item-btn">Grant Access To All Files</a>
                                                    </div>
                                                    @elseif($item->User->user_type == 3)
                                                    <div class="edit">
                                                        <a href="{{route('subadmin_makeUserPreviledge', $item->User->id)}}" class="btn btn-sm btn-dark edit-item-btn">Revoke Access To All Files</a>
                                                    </div>
                                                    @endif
                                                    @if($item->User->user_type == 2)
                                                    <div class="edit">
                                                        <form action="{{route('video_toggle_access', $item->id)}}" method="post">
                                                            @csrf
                                                            <input type="hidden" name="status" value="{{$item->status == 1 ? 0 : 1}}">
                                                            <button type="submit" class="btn btn-sm btn-warning edit-item-btn">
                                                                @if($item->status == 1)
                                                                Revoke Access
                                                                @else
                                                                Grant Access
                                                                @endif
                                                            </button>
                                                        </form>
                                                    </div>
                                                    @endif
                                                    <div class="remove">
                                                        <button class="btn btn-sm btn-danger remove-item-btn" data-link="{{route('video_delete_access', $item->id)}}">Delete</button>
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
@include('includes.admin.call_search_handler', ['url'=>route('video_view_access')])

@stop
