<div class="col-lg-12">
    <div class="row sort-row">
        <div class="col-lg-2 col-md-12 mb-3 sort-div">
            <i class="fas fa-sort-amount-down"></i>
            <select name="sort" id="sort">
                <option value="-id" @if($sort=="-id") selected @endif>Sort by Newest</option>
                <option value="id" @if($sort=='id') selected @endif>Sort by Oldest</option>
                @if($allow_title_sort)
                <option value="title" @if($sort=="title") selected @endif>Sort by A-Z</option>
                <option value="-title" @if($sort=="-title") selected @endif>Sort by Z-A</option>
                @endif
            </select>
        </div>
    </div>
</div>