<script nonce="{{ csp_nonce() }}">
    document.querySelectorAll('.search-handler').forEach(el => {
        el.addEventListener('submit', function(event){
            event.preventDefault();
            callSearchHandler()
        })
        el.addEventListener('change', function(){
            callSearchHandler()
        })
    });
    function callSearchHandler(){
        var str= "";
        var arr = [];
        if(document.getElementById('search').value){
            arr.push(document.getElementById('search').name+"="+document.getElementById('search').value)
        }
        if(document.getElementById('filter').value){
            arr.push(document.getElementById('filter').name+"="+document.getElementById('filter').value)
        }
        str = arr.join('&');
        window.location.replace('{{$url}}?'+str)
        return false;
    }
</script>
