<script type="text/javascript" nonce="{{ csp_nonce() }}">

const validationModal = new JustValidate('#requestAccessForm', {
    errorFieldCssClass: 'is-invalid',
});

validationModal
.addField('#reasonForAccess', [
{
    rule: 'required',
    errorMessage: 'Reason is required',
},
{
    rule: 'customRegexp',
    value: /^[a-z 0-9~%.:_\@\-\/\(\)\\\#\;\[\]\{\}\$\!\&\<\>\'\r\n+=,]+$/i,
    errorMessage: 'Reason is containing invalid characters',
},
])
.addField('#captcha_response', [
    {
        rule: 'required',
        errorMessage: 'Please complete the captcha',
    }
])
.onSuccess(async (event) => {
    event.target.preventDefault;
    const errorToast = (message) =>{
        iziToast.error({
            title: 'Error',
            message: message,
            position: 'bottomCenter',
            timeout:0
        });
    }
    const successToast = (message) =>{
        iziToast.success({
            title: 'Success',
            message: message,
            position: 'bottomCenter',
            timeout:0
        });
    }
    var submitBtn = document.getElementById('SubmitBtn')
    submitBtn.innerHTML = `
        <span class="d-flex align-items-center">
            <span class="spinner-border flex-shrink-0" role="status">
                <span class="visually-hidden"></span>
            </span>
            <span class="flex-grow-1 ms-2">
                &nbsp; Submiting...
            </span>
        </span>
        `
    submitBtn.disabled = true;
    try {
        var formData = new FormData();
        formData.append('message',document.getElementById('reasonForAccess').value)
        formData.append('g-recaptcha-response', document.querySelector('textarea[name="g-recaptcha-response"]').value)
        const response = await axios.post('{{$url}}', formData)
        successToast(response.data.message)
        event.target.reset()
        setTimeout(()=>{
            location.reload()
        }, 1000)
    } catch (error) {
        if(error?.response?.data?.errors?.message){
            validationModal.showErrors({
                '#message': error?.response?.data?.errors?.message[0]
            })
        }
        if(error?.response?.data?.errors?.['g-recaptcha-response']){
            validationModal.showErrors({
                '#captcha_response': error?.response?.data?.errors?.['g-recaptcha-response'][0]
            })
        }
        if(error?.response?.data?.error_popup){
            errorPopup(error?.response?.data?.error_popup)
        }
    } finally{
        submitBtn.innerHTML =  `
            Request
            `
        submitBtn.disabled = false;
        grecaptcha.reset();
        document.getElementById('captcha_response').value = '';
    }
})

function capcthaCallback(val){
    document.getElementById('captcha_response').value = val;
    validationModal.revalidateField('#captcha_response')
}

function capcthaExpired(){
    document.getElementById('captcha_response').value = '';
    validationModal.showErrors({
        '#captcha_response': 'Please complete the captcha'
    })
}

</script>
