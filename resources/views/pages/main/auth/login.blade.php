@extends('layouts.main.auth')

@section('css')

    <meta name="description" content="Sign in with Amrita Janani"/>

    <meta property="og:title" content="Sign In - Amrita Janani" />
    <meta property="og:description" content="Sign in with Amrita Janani" />
    <meta property="og:type" content="website" />
    <meta property="og:image" content="{{ Vite::asset('resources/images/hero/banner4.jpg') }}" />

    <meta name="twitter:title" content="Sign In - Amrita Janani">
    <meta name="twitter:description" content="Sign in with Amrita Janani">
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
            "name": "Sign In"
          }]
        }
    </script>

<style nonce="{{ csp_nonce() }}">
.just-validate-error-label,
.invalid-message {
    color: #fff !important;
}

.btn-captcha {
    background: #ffcc00;
    color: #000;
    border-radius: 5px;
    padding: 5px 15px;
    border: 1px solid #ddd;
    font-size: 10px;
    cursor: pointer;
}

.field {
  position: relative;
  height: 50px;
  width: 100%;
  margin-top: 20px;
  border-radius: 6px;
}

.line {
  position: relative;
  height: 1px;
  width: 100%;
  margin: 36px 0;
  background-color: #d4d4d4;
}
.line::before {
  content: 'OR';
  position: absolute;
  top: 50%;
  left: 50%;
  transform: translate(-50%, -50%);
  background-color: #96171c;
  color: #fff;
  padding: 0 15px;
}
.media-options a {
  display: flex;
  align-items: center;
  justify-content: center;
  text-decoration: none !important;
  transition: all 0.3s ease-in-out;
}
.media-options a:hover {
    opacity: 0.9;
    text-decoration: none !important;
    color: initial
}
a.google {
  color: #222;
  background-color: #fff;
}
a.facebook {
  color: #fff;
  background-color: #4267b2;
}
a.facebook .facebook-icon {
  height: 28px;
  width: 28px;
  color: #0171d3;
  font-size: 20px;
  border-radius: 50%;
  display: flex;
  align-items: center;
  justify-content: center;
  background-color: #fff;
}
.facebook-icon,
img.google-img {
  position: absolute;
  top: 50%;
  left: 15px;
  transform: translateY(-50%);
}
img.google-img {
  height: 20px;
  width: 20px;
  object-fit: cover;
}
a.google {
  border: 1px solid #CACACA;
}
a.google span {
  font-weight: 500;
  opacity: 0.6;
  color: #232836;
}
</style>

@stop

@section('content')

<div class="form-items">
    <!-- <p>Access to the most powerfull tool in amrita janani.</p> -->
    <div class="page-links">
        <a rel="nofollow" aria-label="ssign in" href="{{route('login')}}" class="active">Login</a><a rel="nofollow" aria-label="sign up" href="{{route('signup')}}">Register</a>
    </div>
    <h3>Get access to Amrita Janani by logging in</h3><br/>
    <form action="{{route('signin_authenticate')}}" method="post" id="loginForm">
        @csrf
        <div class="mb-2">
            <input class="form-control" type="email" name="email" id="email" placeholder="E-mail Address*"
                value="{{old('email')}}" required>
            @error('email')
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
            {!! NoCaptcha::display(['data-callback' => 'capcthaCallback', 'data-expired-callback' => 'capcthaExpired']) !!}
            <input type="hidden" id="captcha_response" value="">
            @error('g-recaptcha-response')
                <div class="invalid-message">{{ $message }}</div>
            @enderror
        </div>

        <input type="checkbox" id="chk1" name="remember"><label for="chk1">Remember me</label>
        @error('remember')
            <div class="invalid-message">{{ $message }}</div>
        @enderror
        <div class="form-button">
            <button id="submitBtn" type="submit" class="ibtn">Login</button> <a rel="nofollow"
                href="{{route('forgot_password')}}">Forgot password?</a>
        </div>
    </form>
    {{-- <div class="other-links">
        <span>Or login with</span><a href="#">Facebook</a><a href="#">Google</a><a href="#">Linkedin</a>
    </div> --}}
    <div class="line"></div>
    <div class="media-options">
        <a href="{{route('social.google')}}" class="field google">
          <img src="{{ Vite::asset('resources/images/google.png') }}" alt="" class="google-img">
          <span>
            Login with Google</span>
        </a>
    </div>
    <div class="media-options">
        <a href="{{route('social.facebook')}}" class="field facebook">
          <img src="{{ Vite::asset('resources/images/facebook.png') }}" alt="facebook" class="google-img">
          <span>Login with Facebook</span>
        </a>
    </div>
</div>





@stop

@section('javascript')
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
    .addField('#email', [{
            rule: 'required',
            errorMessage: 'Email is required',
        },
        {
            rule: 'email',
            errorMessage: 'Email is invalid!',
        },
    ])
    .addField('#password', [{
        rule: 'required',
        errorMessage: 'Password is required',
    }])
    .addField('#captcha_response', [
        {
            rule: 'required',
            errorMessage: 'Please complete the captcha',
        }
    ])
    .onSuccess((event) => {
        // event.target.showErrors({ '#email': 'The email is invalid' })
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
