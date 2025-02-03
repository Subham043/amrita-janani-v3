<form  method="get" action="{{$url}}">
    <div class="d-flex justify-content-sm-end">
        <div class="search-box ms-2">
            <input type="text" name="filter[search]" class="form-control search" placeholder="Search..." value="{{request()->query('filter')['search'] ?? ''}}">
            <i class="ri-search-line search-icon"></i>
        </div>
    </div>
</form>
