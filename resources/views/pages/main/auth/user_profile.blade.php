@extends('layouts.main.index')

@section('css')
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/intl-tel-input@24.5.0/build/css/intlTelInput.css" type="text/css" />
<style nonce="{{ csp_nonce() }}">
    .iti{
        width: 100%;
    }
</style>
@stop

@section('content')

    @include('includes.main.sub_menu')

    @include('includes.main.breadcrumb')

    <div class="contact-page-wrapper">

        <div class="contact-form-area section-space--ptb_90">
            <div class="container">
                <div class="row align-items-center">
                    <div class="col-lg-12">
                        <div class="contact-form-wrap ml-lg-5">
                            <form id="contactForm">
                                <div class="contact-form__one row">
                                    <div class="contact-input col-lg-12">
                                        <label for="Name">Name</label>
                                        <div class="contact-inner">
                                            <input name="name" id="name" type="text"
                                                value="{{ Auth::user()->name }}" placeholder="Enter you name">
                                        </div>
                                    </div>

                                    <div class="contact-input col-lg-12">
                                        <label for="Email">Email</label>
                                        <div class="contact-inner">
                                            <input name="email" id="email" type="email"
                                                value="{{ Auth::user()->email }}" placeholder="Your Email Address ">
                                        </div>
                                    </div>

                                    <div class="contact-input col-lg-12">
                                        <label for="Phone">Phone</label>
                                        <div class="contact-inner">
                                            <input name="phone_no" id="phone_no" type="text"
                                                value="{{ Auth::user()->phone }}"
                                                placeholder="Your Phone Number (Optional)">
                                        </div>
                                    </div>


                                    <div class="submit-input col-lg-12">
                                        <button class="submit-btn" type="submit" id="SubmitBtn">Update</button>
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
            formatOnDisplay: false,
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
            .addField('#name', [{
                    rule: 'required',
                    errorMessage: 'Name is required',
                },
                {
                    rule: 'customRegexp',
                    value: /^[a-zA-Z\s]*$/,
                    errorMessage: 'Name is invalid',
                },
            ])
            .addField('#email', [{
                    rule: 'required',
                    errorMessage: 'Email is required',
                },
                {
                    rule: 'email',
                    errorMessage: 'Email is invalid!',
                },
            ])
            .addField('#phone_no', [{
                rule: 'customRegexp',
                value: /^(\+?[1-9])?\d{1,14}$/,
                errorMessage: 'Phone is invalid',
            }, ])
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
                    const response = await axios.post('{{ route('update_userprofile') }}', {
                        'name': document.getElementById('name').value,
                        'email': document.getElementById('email').value,
                        'phone': document.querySelector('input[name="phone"]').value,
                    })
                    successToast(response.data.message)
                } catch (error) {
                    if(error?.response?.data?.errors?.name){
                        validationModal.showErrors({
                            '#name': error?.response?.data?.errors?.name[0]
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
                    if (error?.response?.data?.error) {
                        errorToast(error?.response?.data?.error)
                    }
                } finally {
                    submitBtn.innerHTML = `
            Update
            `
                    submitBtn.disabled = false;
                }
            })
    </script>

    @include('pages.main.content.common.search_js', ['search_url' => route('content_search_query')])
    @include('pages.main.content.common.dashboard_search_handler', [
        'search_url' => route('content_dashboard'),
        'allow_sort' => false
    ])
@stop
