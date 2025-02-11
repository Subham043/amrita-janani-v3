<script type="text/javascript" nonce="{{ csp_nonce() }}">

const validationModal2 = new JustValidate('#reportForm', {
    errorFieldCssClass: 'is-invalid',
});

validationModal2
.addField('#reportMessage', [
{
    rule: 'required',
    errorMessage: 'Message is required',
},
{
    rule: 'customRegexp',
    value: /^[a-z 0-9~%.:_\@\-\/\(\)\\\#\;\[\]\{\}\$\!\&\<\>\'\r\n+=,]+$/i,
    errorMessage: 'Message is containing invalid characters',
},
])
.addField('#captcha_response2', [
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
    var submitBtn = document.getElementById('SubmitBtn2')
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
        formData.append('message',document.getElementById('reportMessage').value)
        formData.append('g-recaptcha-response', document.getElementById('captcha_response2').value)
        const response = await axios.post('{{$url}}', formData)
        successToast(response.data.message)
        event.target.reset()
        setTimeout(()=>{
            location.reload()
        }, 1000)
    } catch (error) {
        if(error?.response?.data?.errors?.message){
            validationModal2.showErrors({
                '#message': error?.response?.data?.errors?.message[0]
            })
        }
        if(error?.response?.data?.errors?.['g-recaptcha-response']){
            validationModal2.showErrors({
                '#captcha_response': error?.response?.data?.errors?.['g-recaptcha-response'][0]
            })
        }
        if(error?.response?.data?.error_popup){
            errorPopup(error?.response?.data?.error_popup)
        }
    } finally{
        submitBtn.innerHTML =  `
            Report
            `
        submitBtn.disabled = false;
        grecaptcha.reset();
        document.getElementById('captcha_response2').value = '';
    }
})

function capcthaCallback2(val){
    document.getElementById('captcha_response2').value = val;
    validationModal2.revalidateField('#captcha_response2')
}

function capcthaExpired2(){
    document.getElementById('captcha_response2').value = '';
    validationModal2.showErrors({
        '#captcha_response2': 'Please complete the captcha'
    })
}

</script>
