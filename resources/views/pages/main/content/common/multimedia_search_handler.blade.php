<script nonce="{{ csp_nonce() }}">
    document.getElementById('search_form_sub_menu').addEventListener('submit', callSearchHandler)
    @if($allow_sort)
    document.getElementById('sort').addEventListener('input', callSearchHandler)
    @endif
    @if($allow_favourite)
    document.getElementById('filter_button').addEventListener('click', callSearchHandler)
    @endif
    function callSearchHandler(event){
        event.preventDefault();
        var str= "";
        var arr = [];

        if(document.getElementById('search').value){
            arr.push("filter[search]="+document.getElementById('search').value)
        }

        @if($allow_sort)
        if(document.getElementById('sort').value){
            arr.push("sort="+document.getElementById('sort').value)
        }
        @endif

        @if($allow_language)
        var inputElems = document.getElementsByName("language");
        var languageArr = [];
        for (var i=0; i<inputElems.length; i++) {
            if (inputElems[i].type === "checkbox" && inputElems[i].checked === true){
                languageArr.push(inputElems[i].value);
            }
        }
        if(languageArr.length > 0){
            languageStr = languageArr.join('_');
            arr.push("filter[language]="+languageStr)
        }
        @endif

        @if($allow_favourite)
        var filter_check = document.getElementById("filter_check");
        if (filter_check.type === "checkbox" && filter_check.checked === true){
            arr.push("filter[favourite]=yes")
        }else{
            arr.push("filter[favourite]=no")
        }
        @endif


        str = arr.join('&');
        window.location.replace('{{$search_url}}?'+str)
    }
</script>
