@extends('layouts.app')

@section('title', 'Register - SKI Capital')

@section('content')
<div class="container">
    <div class="row">
        <!-- Left Side Features -->
        <div class="col-lg-6 mt-4">
            <div class="row">
                <div class="col-md-3">
                    <div class="signup__body-wrapper-ad-panel-details-image">
                        <img src="{{ asset('images/skiRightimg1.png') }}" class="img-responsive skiimgrightside" alt="Credit" width="50" height="50">
                    </div>
                </div>
                <div class="col-md-9">
                    <div class="signup__body-wrapper-ad-panel-details">
                        <div class="signup__body-wrapper-ad-panel-details-info">
                            <div class="signup__body-wrapper-ad-panel-details-info-title">Won't Impact Credit</div>
                            <div class="signup__body-wrapper-ad-panel-details-info-text">Checking your score will not impact your credit.</div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="row">
                <div class="col-md-3">
                    <div class="signup__body-wrapper-ad-panel-details-image">
                        <img src="{{ asset('images/skiRightimg2.png') }}" class="img-responsive skiimgrightside" alt="Free" width="50" height="50">
                    </div>
                </div>
                <div class="col-md-9">
                    <div class="signup__body-wrapper-ad-panel-details">
                        <div class="signup__body-wrapper-ad-panel-details-info">
                            <div class="signup__body-wrapper-ad-panel-details-info-title">Free Forever</div>
                            <div class="signup__body-wrapper-ad-panel-details-info-text">We will never ask for credit card or payment information.</div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="row">
                <div class="col-md-3">
                    <div class="signup__body-wrapper-ad-panel-details-image">
                        <img src="{{ asset('images/skiRightimg3.png') }}" class="img-responsive skiimgrightside" alt="Advice" width="50" height="50">
                    </div>
                </div>
                <div class="col-md-9">
                    <div class="signup__body-wrapper-ad-panel-details">
                        <div class="signup__body-wrapper-ad-panel-details-info">
                            <div class="signup__body-wrapper-ad-panel-details-info-title">Savings and Credit Advice</div>
                            <div class="signup__body-wrapper-ad-panel-details-info-text">Best-in-class credit intelligence identifies savings and advice.</div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Registration Form -->
        <div class="col-lg-6">
            <section>
                <div id="survey_container" class="step_box survey_container">
                    <form id="frmSignup" method="post">
                        @csrf
                        <div id="middle-wizard">
                            <div class="step">
                                <div class="row">
                                    <h3 class="col-md-12">Register</h3>
                                    <div class="col-md-12">
                                        <ul class="data-list">
                                            <li>
                                                <label>Name</label>
                                                <input type="text" id="name" name="name" class="required form-control"
                                                       placeholder="Full Name" required>
                                                <span class="field_error" id="name_error"></span>
                                            </li>

                                            <li>
                                                <label>Mobile Number</label>
                                                <input type="text" id="mobile" name="mobile" class="required form-control mb-5"
                                                       pattern="[789][0-9]{9}" placeholder="Mobile Number" required maxlength="10">
                                                <span class="field_error" id="mobile_error"></span>
                                            </li>

                                            <li>
                                                <label>Email</label>
                                                <input type="email" id="email" name="email" class="required form-control"
                                                       placeholder="Email Id" required>
                                                <span class="field_error" id="email_error"></span>
                                            </li>

                                            <li class="hide_box otp_box">
                                                <label>OTP</label>
                                                <input type="text" id="otp" name="otp" class="required form-control"
                                                       placeholder="Enter OTP" maxlength="6">
                                                <span class="field_error" id="otp_error"></span>
                                            </li>

                                            <li class="hide_box otp_box">
                                                <button type="button" class="forward" id="otp_btn">Verify OTP</button>
                                            </li>
                                        </ul>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div id="bottom-wizard">
                            <button type="submit" name="forward" class="forward" id="signup_btn">Register</button>
                            <div class="mt-3">
                                <p>Already have an account? <a href="{{ route('auth.login') }}">Login here</a></p>
                            </div>
                        </div>
                    </form>
                </div>
            </section>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
$(document).ready(function() {
    let registeredMobile = '';

    // Handle registration form submission
    $('#frmSignup').on('submit', function(e) {
        e.preventDefault();
        clearErrors();

        const name = $('#name').val();
        const mobile = $('#mobile').val();
        const email = $('#email').val();

        if (!name || !mobile || !email) {
            alert('Please fill all required fields');
            return;
        }

        if (mobile.length !== 10) {
            showError('mobile', 'Please enter a valid 10-digit mobile number');
            return;
        }

        showLoader('signup_btn');
        registeredMobile = mobile;

        $.ajax({
            url: '{{ route("auth.register.post") }}',
            method: 'POST',
            data: {
                name: name,
                mobile: mobile,
                email: email
            },
            success: function(response) {
                hideLoader('signup_btn', 'Register');
                if (response.success) {
                    alert(response.message);
                    // Show OTP field
                    $('.otp_box').removeClass('hide_box');
                    $('#name, #mobile, #email').prop('readonly', true);
                    $('#signup_btn').hide();
                } else {
                    showError('mobile', response.message);
                }
            },
            error: function(xhr) {
                hideLoader('signup_btn', 'Register');
                if (xhr.responseJSON && xhr.responseJSON.errors) {
                    $.each(xhr.responseJSON.errors, function(key, value) {
                        showError(key, value[0]);
                    });
                } else {
                    alert('Error during registration. Please try again.');
                }
            }
        });
    });

    // Handle OTP verification
    $('#otp_btn').on('click', function() {
        clearErrors();

        const otp = $('#otp').val();

        if (!otp || otp.length !== 6) {
            showError('otp', 'Please enter a valid 6-digit OTP');
            return;
        }

        showLoader('otp_btn');

        $.ajax({
            url: '{{ route("auth.verifyRegistrationOtp") }}',
            method: 'POST',
            data: {
                mobile: registeredMobile,
                otp: otp
            },
            success: function(response) {
                hideLoader('otp_btn', 'Verify OTP');
                if (response.success) {
                    alert(response.message);
                    window.location.href = response.redirect || '{{ route("kyc.form") }}';
                } else {
                    showError('otp', response.message);
                    if (response.remaining_attempts !== undefined) {
                        showError('otp', 'Remaining attempts: ' + response.remaining_attempts);
                    }
                }
            },
            error: function(xhr) {
                hideLoader('otp_btn', 'Verify OTP');
                if (xhr.responseJSON && xhr.responseJSON.errors) {
                    $.each(xhr.responseJSON.errors, function(key, value) {
                        showError(key, value[0]);
                    });
                } else {
                    alert('Error verifying OTP. Please try again.');
                }
            }
        });
    });

    // Allow OTP submission on Enter key
    $('#otp').on('keypress', function(e) {
        if (e.which === 13) {
            e.preventDefault();
            $('#otp_btn').click();
        }
    });
});
</script>
@endpush
