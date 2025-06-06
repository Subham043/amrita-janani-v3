@extends('layouts.admin.auth')



@section('content')

<div class="row justify-content-center">
    <div class="col-md-8 col-lg-6 col-xl-5">
        <div class="card mt-4">

            <div class="card-body p-4">
                <div class="text-center mt-2">
                    <h5 class="text-primary">Forgot Password?</h5>
                    <p class="text-muted">Reset password with Amrita Janani</p>

                </div>

                <div class="alert alert-borderless alert-warning text-center mb-2 mx-2" role="alert">
                    Enter your email and instructions will be sent to you!
                </div>
                <div class="p-2">
                    <form id="forgotPasswordForm" method="post" action="{{route('requestForgotPassword')}}">
                    @csrf
                        <div class="mb-4">
                            <label class="form-label">Email</label>
                            <input type="email" class="form-control" id="email" name="email" value="{{old('email')}}" placeholder="Enter Email">
                            @error('email')
                                <div class="invalid-message">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="mb-3">
                            {!! NoCaptcha::display(['data-callback' => 'capcthaCallback', 'data-expired-callback' => 'capcthaExpired']) !!}
                            <input type="hidden" id="captcha_response" value="">
                            @error('g-recaptcha-response')
                                <div class="invalid-message">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="text-center mt-4">
                            <button class="btn btn-success w-100" type="submit">Send Reset Link</button>
                        </div>
                    </form><!-- end form -->
                </div>
            </div>
            <!-- end card body -->
        </div>
        <!-- end card -->

        <div class="mt-4 text-center">
            <p class="mb-0">Wait, I remember my password... <a href="{{route('signin')}}" class="fw-semibold text-primary text-decoration-underline"> Click here </a> </p>
        </div>

    </div>
</div>

@stop

@section('javascript')

<script type="text/javascript" nonce="{{ csp_nonce() }}">
    $(function () {
        $('#email').focus();

    });

// initialize the validation library
const validation = new JustValidate('#forgotPasswordForm', {
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
