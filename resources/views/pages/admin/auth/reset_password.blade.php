@extends('layouts.admin.auth')



@section('content')

<div class="row justify-content-center">
    <div class="col-md-8 col-lg-6 col-xl-5">
        <div class="card mt-4">

            <div class="card-body p-4">
                <div class="text-center mt-2">
                    <h5 class="text-primary">Reset Password !</h5>
                    <p class="text-muted">Enter the following details to reset password.</p>
                </div>
                <div class="p-2 mt-4">
                    <form id="loginForm" method="post" action="{{Request::getRequestUri()}}">
                    @csrf
                        <div class="mb-4">
                            <label class="form-label">Email</label>
                            <input type="email" class="form-control" id="email" name="email" value="{{old('email')}}" placeholder="Enter Email">
                            @error('email')
                                <div class="invalid-message">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="mb-3">
                            <label class="form-label" for="password">Password</label>
                            <div class="position-relative auth-pass-inputgroup mb-3">
                                <input type="password" class="form-control pe-5" placeholder="Enter password" id="password" name="password">
                                <button class="btn btn-link position-absolute end-0 top-0 text-decoration-none text-muted" type="button" id="password-addon"><i class="ri-eye-fill align-middle"></i></button>
                            </div>
                            @error('password')
                                <div class="invalid-message">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="mb-3">
                            <label for="password_confirmation" class="form-label">Confirm Password</label>
                            <input type="password" class="form-control" id="password_confirmation" name="password_confirmation" placeholder="Enter the password again">
                            @error('password_confirmation')
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

                        <div class="mt-4">
                            <button class="btn btn-success w-100" type="submit">Reset</button>
                        </div>

                    </form>
                </div>
            </div>
            <!-- end card body -->
        </div>
        <!-- end card -->

    </div>
</div>

@stop

@section('javascript')
<!-- password-addon init -->
<script src="{{ asset('admin/js/pages/password-addon.init.js') }}"></script>
<script type="text/javascript" nonce="{{ csp_nonce() }}">
    $(function () {
        $('#email').focus();

    });

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
  .addField('#password', [
    {
      rule: 'required',
      errorMessage: 'Password is required',
    }
  ])
  .addField('#password_confirmation', [
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
