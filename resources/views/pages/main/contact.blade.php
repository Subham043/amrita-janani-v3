@extends('layouts.main.index')

@section('css')
    <meta name="description" content="Get in touch with Amrita Janani"/>

    <meta property="og:title" content="Contact Us - Amrita Janani" />
    <meta property="og:description" content="Get in touch with Amrita Janani" />
    <meta property="og:type" content="website" />
    <meta property="og:image" content="{{ Vite::asset('resources/images/hero/banner4.jpg') }}" />

    <meta name="twitter:title" content="Contact Us - Amrita Janani">
    <meta name="twitter:description" content="Get in touch with Amrita Janani">
    <meta name="twitter:image" content="{{ Vite::asset('resources/images/hero/banner4.jpg') }}">

    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/intl-tel-input@24.5.0/build/css/intlTelInput.css" type="text/css" />

    <script type="application/ld+json" nonce="{{ csp_nonce() }}">
        {
          "@context": "https://schema.org",
          "@type": "BreadcrumbList",
          "itemListElement": [{
            "@type": "ListItem",
            "position": 1,
            "name": "Amrita Janani",
            "item": "{{url()->to('/')}}"
          },{
            "@type": "ListItem",
            "position": 2,
            "name": "Contact Us"
          }]
        }
    </script>

<style nonce="{{ csp_nonce() }}">
    .contact-form__one .contact-input .contact-inner textarea {
        border-radius: 25px;
        border: 1px solid #ddd;
        padding: 10px 20px;
        width: 100%;
        font-style: italic;
    }
    .iti{
        width: 100%;
    }
</style>
@stop

@section('content')

@include('includes.main.breadcrumb')

<div class="contact-page-wrapper section-space--pt_120">
            <div class="container">
                <div class="row">
                    <div class="col-lg-4">
                        <div class="single-contact-info">
                            <div class="contact-icon">
                                <i class="flaticon-placeholder"></i>
                            </div>
                            <div class="contact-info">
                                <h4>Address</h4>
                                <p>Devipuram<br/>
                                Via Nidanam Doddi, Sabbavaram (M),<br/>
                                Visakhapatnam District,<br/>
                                Andhra Pradesh - 531035<br/>
                                India</p>
                            </div>
                        </div>
                    </div>
                    <div class="col-lg-4">
                        <div class="single-contact-info">
                            <div class="contact-icon">
                                <i class="flaticon-call"></i>
                            </div>
                            <div class="contact-info">
                                <h4>Phone</h4>
                                <p><a rel="nofollow" aria-label="phone 1" href="tel:+919440845333">+91 94408 45333</a><br>
                                    <a rel="nofollow" aria-label="phone 2" href="tel:+918340005500">+91 83400 05500</a></p>
                            </div>
                        </div>
                    </div>
                    <div class="col-lg-4">
                        <div class="single-contact-info">
                            <div class="contact-icon">
                                <i class="flaticon-paper-plane-1"></i>
                            </div>
                            <div class="contact-info">
                                <h4>Mail</h4>
                                <p><a rel="nofollow" aria-label="email" href="mailto:admin@amrita-janani.org">admin@amrita-janani.org</a></p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>


            <div class="contact-form-area section-space--ptb_120">
                <div class="container">
                    <div class="row align-items-center">
                        <div class="col-lg-12">
                            <div class="contact-form-wrap ml-lg-5">
                                <h3 class="title mb-40">Get In Touch With Us</h3>
                                <form id="contactForm">
                                    <div class="contact-form__one row">
                                        <div class="contact-input col-lg-6">
                                            <label for="Name">Name</label>
                                            <div class="contact-inner">
                                                <input name="name" id="name" type="text" placeholder="Enter you name">
                                            </div>
                                        </div>

                                        <div class="contact-input col-lg-6">
                                            <label for="Phone">Phone</label>
                                            <div class="contact-inner">
                                                <input name="phone_no" id="phone_no" type="text" placeholder="Your Phone Number (Optional)">
                                            </div>
                                        </div>

                                        <div class="contact-input col-lg-6">
                                            <label for="Email">Email</label>
                                            <div class="contact-inner">
                                                <input name="email" id="email" type="email" placeholder="Your Email Address ">
                                            </div>
                                        </div>

                                        <div class="contact-input col-lg-6">
                                            <label for="subject">Subject</label>
                                            <div class="contact-inner">
                                                <input name="subject" id="subject" type="text" placeholder="Enter you subject">
                                            </div>
                                        </div>

                                        <div class="contact-input col-lg-12">
                                            <label for="subject">Message</label>
                                            <div class="contact-inner">
                                                <textarea name="message" id="message" cols="30" rows="5" placeholder="Enter you message"></textarea>
                                            </div>
                                        </div>

                                        <div class="contact-input col-lg-6">
                                            {!! NoCaptcha::display(['data-callback' => 'capcthaCallback', 'data-expired-callback' => 'capcthaExpired']) !!}
                                            <input type="hidden" id="captcha_response" value="">
                                        </div>

                                        <div class="submit-input col-lg-12">
                                            <button class="submit-btn" type="submit" id="SubmitBtn">Submit</button>
                                            <p class="form-messege"></p>
                                        </div>
                                    </div>
                                </form>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>


@stop

@section('javascript')
<script src="https://cdn.jsdelivr.net/npm/intl-tel-input@24.5.0/build/js/intlTelInput.min.js"></script>
<script src="{{ asset('admin/js/pages/just-validate.production.min.js') }}"></script>
<script src="{{ asset('main/js/plugins/axios.min.js') }}"></script>

<script type="text/javascript" nonce="{{ csp_nonce() }}">

const countryData = window.intlTelInput(document.querySelector("#phone_no"), {
    utilsScript: "https://cdn.jsdelivr.net/npm/intl-tel-input@24.5.0/build/js/utils.js",
    autoInsertDialCode: true,
    initialCountry: "in",
    nationalMode: false,
    formatAsYouType: false,
    hiddenInput: (telInputName) => ({
      phone: "phone",
      country: "country_code"
    }),
    geoIpLookup: callback => {
        fetch("https://ipapi.co/json")
        .then(res => res.json())
        .then(data => callback(data.country_code))
        .catch((e) => callback("in"));
    },
});

const validationModal = new JustValidate('#contactForm', {
    errorFieldCssClass: 'is-invalid',
});

validationModal
.addField('#name', [
{
    rule: 'required',
    errorMessage: 'Name is required',
},
{
    rule: 'customRegexp',
    value: /^[a-zA-Z\s]*$/,
    errorMessage: 'Name is invalid',
},
])
.addField('#email', [
{
    rule: 'required',
    errorMessage: 'Email is required',
},
{
    rule: 'email',
    errorMessage: 'Email is invalid!',
},
])
.addField('#phone_no', [
    {
        rule: 'customRegexp',
        value: /^\+?[1-9]\d{1,14}$/,
        errorMessage: 'Phone is invalid',
    },
  ], {
    errorsContainer: '#phone_error'
})
.addField('#subject', [
{
    rule: 'required',
    errorMessage: 'Subject is required',
},
{
    rule: 'customRegexp',
    value: /^[a-z 0-9~%.:_\@\-\/\(\)\\\#\;\[\]\{\}\$\!\&\<\>\'\r\n+=,]+$/i,
    errorMessage: 'Subject is invalid',
},
])
.addField('#message', [
{
    rule: 'required',
    errorMessage: 'Message is required',
},
{
    rule: 'customRegexp',
    value: /^[a-z 0-9~%.:_\@\-\/\(\)\\\#\;\[\]\{\}\$\!\&\<\>\'\r\n+=,]+$/i,
    errorMessage: 'Message is invalid',
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
        var system_info = null;
        const systemInfo = await axios.get("https://ipapi.co/json");
        system_info = systemInfo.data ?? null;
        const response = await axios.post('{{route('contact_ajax')}}', {
            'name': document.getElementById('name').value,
            'subject': document.getElementById('subject').value,
            'email': document.getElementById('email').value,
            'phone': document.querySelector('input[name="phone"]').value,
            'message': document.getElementById('message').value,
            'g-recaptcha-response': document.querySelector('textarea[name="g-recaptcha-response"]').value,
            'system_info': (system_info) ? JSON.stringify(system_info) : null
        })
        successToast(response.data.message)
        event.target.reset()
    } catch (error) {
        if(error?.response?.data?.errors?.name){
            validationModal.showErrors({
                '#name': error?.response?.data?.errors?.name[0]
            })
        }
        if(error?.response?.data?.errors?.subject){
            validationModal.showErrors({
                '#subject': error?.response?.data?.errors?.subject[0]
            })
        }
        if(error?.response?.data?.errors?.email){
            validationModal.showErrors({
                '#email': error?.response?.data?.errors?.email[0]
            })
        }
        if(error?.response?.data?.errors?.phone){
            validationModal.showErrors({
                '#phone_no': error?.response?.data?.errors?.phone[0]
            })
        }
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
            Submit
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

@stop
