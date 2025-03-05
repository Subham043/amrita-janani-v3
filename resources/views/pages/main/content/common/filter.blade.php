<div class="filter-holder">
    <hr>

    <button class="accordion active">Other Filter</button>
    <div class="panel">
        <ul>
            <li>
                <label for="filter_check">
                <input type="checkbox" id="filter_check" name="filter" @if($favourite) checked @endif>
                    My Favourite Audio
                </label>
            </li>
        </ul>
    </div>
    <hr>

    @if($allow_language_filter && count($languages) > 0)
    <button class="accordion active">Language</button>
    <div class="panel">
        <ul>

            @foreach($languages as $languages)
            <li>
                <label for="language{{$languages->id}}">
                    <input type="checkbox" name="language" id="language{{$languages->id}}" value="{{$languages->id}}" @if(in_array($languages->id, $selected_languages)) checked @endif>
                    {{$languages->name}}
                </label>
            </li>
            @endforeach

        </ul>
    </div>
    <hr>
    @endif


</div>
<div class="text-left">
    <button id="filter_button" class="filter_button"> Apply </button>
    <a href="{{$url}}" class="filter_button"> Clear </a>
</div>