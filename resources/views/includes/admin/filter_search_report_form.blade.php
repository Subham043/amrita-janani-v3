<form  method="get" action="{{$url}}" class="col-sm-auto search-handler">
    <div class="d-flex justify-content-sm-end">
        <div class="search-box ms-2">
            <select name="filter[status]" id="filter" class="form-control search-handler">
                <option value="all" @if(($filter_status=='all')) selected @endif>All</option>
                <option value="0" @if(($filter_status=='0')) selected @endif>Pending</option>
                <option value="1" @if(($filter_status=='1')) selected @endif>In Progress</option>
                <option value="2" @if(($filter_status=='2')) selected @endif>Completed</option>
            </select>
            <i class="ri-arrow-up-down-line search-icon"></i>
        </div>
    </div>
</form>
<form  method="get" class="col-sm-auto search-handler" action="{{$url}}">
    <div class="d-flex justify-content-sm-end">
        <div class="search-box ms-2">
            <input type="text" name="filter[search]" id="search" class="form-control search" placeholder="Search..." value="{{$filter_search}}">
            <i class="ri-search-line search-icon"></i>
        </div>
    </div>
</form>
