<script nonce="{{ csp_nonce() }}">
    document.getElementById('search_form_sub_menu').addEventListener('submit', callSearchHandler)
    @if($allow_sort)
    document.getElementById('sort').addEventListener('input', callSearchHandler)
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


        str = arr.join('&');
        window.location.replace('{{$search_url}}?'+str)
        return false;
    }
</script>