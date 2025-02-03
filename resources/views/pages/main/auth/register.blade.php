@extends('layouts.main.auth')

@section('css')
    <meta name="description" content="Sign up with Amrita Janani"/>

    <meta property="og:title" content="Sign Up - Amrita Janani" />
    <meta property="og:description" content="Sign up with Amrita Janani" />
    <meta property="og:type" content="website" />
    <meta property="og:image" content="{{ Vite::asset('resources/images/hero/banner4.jpg') }}" />

    <meta name="twitter:title" content="Sign Up - Amrita Janani">
    <meta name="twitter:description" content="Sign up with Amrita Janani">
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
            "name": "Sign Up"
          }]
        }
    </script>

<style nonce="{{ csp_nonce() }}">
    .just-validate-error-label, .invalid-message{
        color: #fff !important;
    }
    .btn-captcha{
        background: #ffcc00;
        color: #000;
        border-radius: 5px;
        padding: 5px 15px;
        border: 1px solid #ddd;
        font-size: 10px;
        cursor: pointer;
    }
    .text-underline{
        text-decoration: underline;
    }
    .iti{
        width: 100%;
    }
</style>
@stop

@section('content')

<div class="form-items">
  <!-- <p>Access to the most powerfull tool in amrita janani.</p> -->
  <div class="page-links">
    <a rel="nofollow" aria-label="sign in" href="{{route('login')}}">Login</a><a rel="nofollow" aria-label="sign up" href="{{route('signup')}}" class="active">Register</a>
  </div>
  <h3>Register to get access to Amrita Janani</h3><br/>
    <form action="{{route('signup_store')}}" method="post" id="loginForm">
        @csrf
        <div class="mb-2">
            <input class="form-control" type="text" name="name" id="name" placeholder="Name*" value="{{old('name')}}" required>
            @error('name')
                <div class="invalid-message">{{ $message }}</div>
            @enderror
        </div>
        <div class="mb-2">
        <input class="form-control" type="email" name="email" id="email" placeholder="E-mail Address*" value="{{old('email')}}" required>
        @error('email')
            <div class="invalid-message">{{ $message }}</div>
        @enderror
        </div>
        <div class="mb-2">
        <input class="form-control" type="text" name="phone_no" id="phone_no" placeholder="Phone Number" value="{{old('phone')}}" required>
        <div class="invalid-message" id="phone_error"></div>
        @error('phone')
            <div class="invalid-message">{{ $message }}</div>
        @enderror
        </div>
        <div class="mb-2">
        <input class="form-control" type="password" name="password" id="password" placeholder="Password*" required>
        @error('password')
            <div class="invalid-message">{{ $message }}</div>
        @enderror
        </div>
        <div class="mb-2">
        <input class="form-control" type="password" name="cpassword" id="cpassword" placeholder="Confirm Password*" required>
        @error('cpassword')
            <div class="invalid-message">{{ $message }}</div>
        @enderror
        </div>
        <div class="mb-2">
          {!! NoCaptcha::display(['data-callback' => 'capcthaCallback', 'data-expired-callback' => 'capcthaExpired']) !!}
          <input type="hidden" id="captcha_response" value="">
          @error('g-recaptcha-response')
            <div class="invalid-message">{{ $message }}</div>
          @enderror
        </div>
        <div class="mb-2">
        <input type="checkbox" id="chk1"><label for="chk1">Accept <a rel="nofollow" aria-label="privacy page" href="{{route('privacy_policy')}}" class="text-white font-weight-bold text-underline" target="_blank">Terms & Condtions</a></label>
        </div>
        <div class="form-button">
            <button id="btnsubmit" type="submit" class="ibtn">Register</button> <a rel="nofollow" aria-label="sign in" href="{{route('login')}}">Already a registered user?</a>
        </div>
    </form>
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
        .catch(() => callback("in"));
    },
});

// initialize the validation library
const validation = new JustValidate('#loginForm', {
      errorFieldCssClass: 'is-invalid',
      focusInvalidField: true,
      lockForm: true,
});
// apply rules to form fields
validation
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
        value: /^(\+?[1-9])?\d{1,14}$/,
        errorMessage: 'Phone is invalid',
    },
  ], {
    errorsContainer: '#phone_error'
  })
  .addField('#password', [
    {
      rule: 'required',
      errorMessage: 'Password is required',
    },
    {
        rule: 'customRegexp',
        value: /^[a-z 0-9~%.:_\@\-\/\(\)\\\#\;\[\]\{\}\$\!\&\<\>\'\r\n+=,]+$/i,
        errorMessage: 'Password is invalid',
    },
  ])
  .addField('#cpassword', [
    {
      rule: 'required',
      errorMessage: 'Confirm Password is required',
    },
    {
        validator: (value, fields) => {
        if (fields['#password'] && fields['#password'].elem) {
            const repeatPasswordValue = fields['#password'].elem.value;

            return value === repeatPasswordValue;
        }

        return true;
        },
        errorMessage: 'Password and Confirm Password must be same',
    },
  ])
  .addField('#chk1', [
    {
      rule: 'required',
      errorMessage: 'Please accept the terms & conditions',
    }
  ])
  .addField('#captcha_response', [
    {
      rule: 'required',
      errorMessage: 'Please complete the captcha',
    }
  ])
  .onSuccess((event) => {
    event.target.submit();
  });

  function capcthaCallback(val){
    document.getElementById('captcha_response').value = val;
    validation.revalidateField('#captcha_response')
  }

  function capcthaExpired(){
      document.getElementById('captcha_response').value = '';
      validation.showErrors({
        '#captcha_response': 'Please complete the captcha'
      })
  }
</script>
@stop
