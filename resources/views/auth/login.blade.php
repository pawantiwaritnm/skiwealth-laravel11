@extends('layouts.guest')

@section('title', 'Login - SKI Capital')

@section('content')
<div class="container">
    <div class="row">
        <div class="col-lg-12">
            <h3>Open your Trading and Demat Account in 5 quick steps</h3>
        </div>

        <!-- Login Form -->
        <div class="col-lg-6">
            <section>
                <div id="survey_container" class="step_box survey_container">
                    <form id="frmLogin" method="post">
                        @csrf
                        <div id="middle-wizard">
                            <div class="step">
                                <div class="row">
                                    <h3 class="col-md-12">Login</h3>
                                    <div class="col-md-12">
                                        <ul class="data-list">
                                            <li>
                                                <label>Mobile Number</label>
                                                <input type="text" id="mobile" name="mobile" class="required form-control mb-5"
                                                       pattern="[789][0-9]{9}" placeholder="Mobile Number" required maxlength="10">
                                                <span class="field_error" id="mobile_error"></span>
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
                            <button type="submit" name="forward" class="forward" id="signin_btn">Send OTP</button>
                            <div class="mt-3">
                                <p>Don't have an account? <a href="{{ route('auth.register') }}">Register here</a></p>
                            </div>
                        </div>
                    </form>
                </div>
            </section>
        </div>

        <!-- Right Side Features -->
        <div class="col-lg-6 mt-4">
            <div class="row">
                <div class="col-md-3">
                    <div class="signup__body-wrapper-ad-panel-details-image">
                        <img src="{{ asset('images/skiRightimg1.png') }}" class="img-responsive skiimgrightside" alt="Expertise" width="50" height="50">
                    </div>
                </div>
                <div class="col-md-9">
                    <div class="signup__body-wrapper-ad-panel-details">
                        <div class="signup__body-wrapper-ad-panel-details-info">
                            <div class="signup__body-wrapper-ad-panel-details-info-title">30 Years of Expertise</div>
                            <div class="signup__body-wrapper-ad-panel-details-info-text">
                                Our financial advisory teams have been providing unparalleled services for more than 3 decades
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="row">
                <div class="col-md-3">
                    <div class="signup__body-wrapper-ad-panel-details-image">
                        <img src="{{ asset('images/skiRightimg2.png') }}" class="img-responsive skiimgrightside" alt="Service" width="50" height="50">
                    </div>
                </div>
                <div class="col-md-9">
                    <div class="signup__body-wrapper-ad-panel-details">
                        <div class="signup__body-wrapper-ad-panel-details-info">
                            <div class="signup__body-wrapper-ad-panel-details-info-title">A high level of personal service</div>
                            <div class="signup__body-wrapper-ad-panel-details-info-text">
                                Your Relationship Manager will work with you to create a bespoke strategy, tailored to your objectives
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="row">
                <div class="col-md-3">
                    <div class="signup__body-wrapper-ad-panel-details-image">
                        <img src="{{ asset('images/skiRightimg3.png') }}" class="img-responsive skiimgrightside" alt="Products" width="50" height="50">
                    </div>
                </div>
                <div class="col-md-9">
                    <div class="signup__body-wrapper-ad-panel-details">
                        <div class="signup__body-wrapper-ad-panel-details-info">
                            <div class="signup__body-wrapper-ad-panel-details-info-title">Complete Financial Products and Services</div>
                            <div class="signup__body-wrapper-ad-panel-details-info-text">
                                Offering all the financial and insurance products to meet your diverse portfolio needs
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
$(document).ready(function() {
    let mobileNumber = '';

    // Handle login form submission (Send OTP)
    $('#frmLogin').on('submit', function(e) {
        e.preventDefault();
        clearErrors();

        mobileNumber = $('#mobile').val();

        if (!mobileNumber || mobileNumber.length !== 10) {
            showError('mobile', 'Please enter a valid 10-digit mobile number');
            return;
        }

        showLoader('signin_btn');

        $.ajax({
            url: '{{ route("auth.sendLoginOtp") }}',
            method: 'POST',
            data: {
                mobile: mobileNumber
            },
            success: function(response) {
                hideLoader('signin_btn', 'Send OTP');
                if (response.success) {
                    alert(response.message);
                    // Show OTP field
                    $('.otp_box').removeClass('hide_box');
                    $('#mobile').prop('readonly', true);
                    $('#signin_btn').hide();
                } else {
                    showError('mobile', response.message);
                }
            },
            error: function(xhr) {
                hideLoader('signin_btn', 'Send OTP');
                if (xhr.responseJSON && xhr.responseJSON.errors) {
                    $.each(xhr.responseJSON.errors, function(key, value) {
                        showError(key, value[0]);
                    });
                } else {
                    alert('Error sending OTP. Please try again.');
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
            url: '{{ route("auth.verifyLoginOtp") }}',
            method: 'POST',
            data: {
                mobile: mobileNumber,
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
