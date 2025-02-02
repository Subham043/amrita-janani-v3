@extends('layouts.main.auth')

@section('css')
    <meta name="description" content="Forgot password for Amrita Janani"/>

    <meta property="og:title" content="Forgot Password - Amrita Janani" />
    <meta property="og:description" content="Forgot password for Amrita Janani" />
    <meta property="og:type" content="website" />
    <meta property="og:image" content="{{ Vite::asset('resources/images/hero/banner4.jpg') }}" />

    <meta name="twitter:title" content="Forgot Password - Amrita Janani">
    <meta name="twitter:description" content="Forgot password for Amrita Janani">
    <meta name="twitter:image" content="{{ Vite::asset('resources/images/hero/banner4.jpg') }}">

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
            "name": "Forgot Password"
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
</style>

@stop

@section('content')

<div class="form-items">
    <h3>Forgot Password</h3>
    <p>To reset your password, enter the email address you use to sign in to Amrita Janani.</p>
    <form action="{{route('forgot_password_request')}}" method="post" id="loginForm">
        @csrf
        <div class="mb-2">
            <input class="form-control" type="email" name="email" id="email" placeholder="E-mail Address" value="{{old('email')}}" required>
            @error('email')
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
        <div class="form-button">
            <button id="submitBtn" type="submit" class="ibtn">Submit</button> <a rel="nofollow" aria-label="sign in" href="{{route('login')}}">Remember your password?</a>
        </div>
    </form>
</div>





@stop

@section('javascript')
<script src="{{ asset('admin/js/pages/just-validate.production.min.js') }}"></script>
<script src="{{ asset('main/js/plugins/axios.min.js') }}"></script>
<script type="text/javascript" nonce="{{ csp_nonce() }}">

// initialize the validation library
const validation = new JustValidate('#loginForm', {
      errorFieldCssClass: 'is-invalid',
      focusInvalidField: true,
      lockForm: true,
});
// apply rules to form fields
validation
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
