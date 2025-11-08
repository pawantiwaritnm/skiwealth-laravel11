@extends('layouts.app')

@section('title', 'In-Person Verification - SKI Capital')

@section('content')
<style>
    .signup__body-wrapper-ad-panel-details {
        padding: 0 0 20px 0;
        margin-top: 6%;
    }
    .hide_box { display: none; }
    .error { color: red; font-size: 12px; margin-top: 5px; }
    .survey_container { background: #fff; padding: 30px; border-radius: 8px; box-shadow: 0 0 10px rgba(0,0,0,0.1); }
    #bottom-wizard { margin-top: 30px; }
    .forward { padding: 10px 30px; }
    .data-list { list-style: none; padding: 0; }
    .data-list li { margin-bottom: 15px; }
    .data-list label { font-weight: 600; margin-bottom: 5px; display: block; }
</style>

<div class="container camera_page">
    <div class="row">
        <div class="col-lg-8">
            <section>
                <div id="survey_container" class="step_box survey_container">
                    <form id="check_user" method="post" role="form">
                        @csrf
                        <div id="middle-wizard">
                            <div class="step">
                                <div class="row">
                                    <h3 class="col-md-12">Enter your mobile number to begin In-Person Verification (IPV) with your webcam or mobile camera</h3>
                                    <div class="col-md-12">
                                        <ul class="data-list">
                                            <li>
                                                <label>Mobile Number</label>
                                                <input type="text" id="mobile" name="mobile" class="required form-control mb-5" placeholder="Mobile Number" pattern="[789][0-9]{9}" maxlength="10" required>
                                                <span class="field_error error" id="mobile_error"></span>
                                            </li>
                                            <li id="captcha_box">
                                                <div class="g-recaptcha" data-sitekey="{{ config('services.recaptcha.site_key_ipv') }}"></div>
                                                <div class="g-recaptcha-response error"></div>
                                            </li>
                                            <li id="captcha_box">
                                                <div class="user_error error"></div>
                                            </li>
                                            <li class="hide_box otp_box">
                                                <label>OTP</label>
                                                <input type="text" id="otp" name="otp" class="required form-control" placeholder="OTP" maxlength="4">
                                                <span class="field_error error" id="otp_error"></span>
                                            </li>
                                            <li class="hide_box otp_box">
                                                <button type="button" class="forward btn btn-primary" id="otp_btn" onclick="user_verify_otp()">Verify OTP</button>
                                            </li>
                                        </ul>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div id="bottom-wizard">
                            <button type="submit" name="forward" class="forward btn btn-primary" id="check_user_btn">Submit</button>
                        </div>
                    </form>
                </div>
            </section>
        </div>
        <div class="col-md-4">
            <div class="camera_img">
                <img src="{{ asset('images/ipv_camera.png') }}" alt="IPV Camera" class="img-fluid">
            </div>
        </div>
    </div>
</div>

@push('scripts')
<script src="https://www.google.com/recaptcha/api.js" async defer></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/jquery-validate/1.19.3/jquery.validate.min.js"></script>
<script>
// Only numbers
function onlyNumberKey(evt) {
    var asciiCode = (evt.which) ? evt.which : evt.keyCode;
    if (asciiCode > 47 && asciiCode < 58)
        return true;
    return false;
}

// OTP form enter key
function userotpFRM(evt) {
    if (evt.keyCode == 13)
        user_verify_otp();
}

// Verify OTP
function user_verify_otp() {
    jQuery(".field_error").html("");
    jQuery("#otp_btn").html("Please wait...");
    jQuery("#otp_btn").attr("disabled", true);
    var otp = jQuery("#otp").val();

    jQuery.ajax({
        url: '{{ route("ipv.verifyOtp") }}',
        type: 'post',
        data: {
            otp: otp,
            _token: '{{ csrf_token() }}'
        },
        success: function(result) {
            if (result.success) {
                window.location.href = '{{ route("ipv.camera") }}';
            } else {
                jQuery("#otp_error").html(result.message || "Please enter correct OTP");
                jQuery("#otp_btn").html("Verify OTP");
                jQuery("#otp_btn").attr("disabled", false);
            }
        },
        error: function(xhr) {
            jQuery("#otp_error").html("Error verifying OTP. Please try again.");
            jQuery("#otp_btn").html("Verify OTP");
            jQuery("#otp_btn").attr("disabled", false);
        }
    });
}

$(document).ready(function() {
    // Form validation
    $("#check_user").validate({
        rules: {
            mobile: {
                required: true,
                number: true,
                maxlength: 10,
                minlength: 10,
            },
        },
        errorElement: "span",
        errorPlacement: function(error, element) {
            element.closest("li").append(error);
        },
        submitHandler: function(form) {
            jQuery(".field_error").html("");
            $('.user_error').text('');
            $('.error').text('');
            jQuery("#check_user_btn").html("Please wait...");
            jQuery("#check_user_btn").attr("disabled", true);

            jQuery.ajax({
                url: '{{ route("ipv.checkUser") }}',
                type: 'post',
                data: jQuery("#check_user").serialize(),
                success: function(result) {
                    if (result.status == 1) {
                        jQuery("#mobile").attr("disabled", "true");
                        jQuery(".otp_box").removeClass('hide_box');
                        jQuery("#bottom-wizard").hide();
                        jQuery("#captcha_box").hide();
                    } else if (result.status == 0) {
                        $('#mobile_error').text(result.error);
                        jQuery("#check_user_btn").attr("disabled", false);
                        jQuery("#check_user_btn").html("Submit");
                        setTimeout(function() {
                            window.location.href = '/';
                        }, 2000);
                    } else if (result.status == -3) {
                        $('.user_error').text(result.error);
                        jQuery("#check_user_btn").attr("disabled", false);
                        jQuery("#check_user_btn").html("Submit");
                    } else if (result.status == -2) {
                        jQuery("#check_user_btn").html("Submit");
                        jQuery("#check_user_btn").attr("disabled", false);
                        grecaptcha.reset();
                        var res = result.validation_array;
                        for (const error in res) {
                            $(`.${error}`).html(res[error]);
                            $("html, body").animate({
                                scrollTop: $(`#${error}`).offset().top + "px",
                            }, "fast");
                        }
                    }
                },
                error: function(xhr) {
                    jQuery("#check_user_btn").html("Submit");
                    jQuery("#check_user_btn").attr("disabled", false);
                    alert('Error: Please try again');
                }
            });
        },
    });

    // OTP input enter key
    $('#otp').on('keypress', function(e) {
        if (e.which == 13) {
            user_verify_otp();
        }
    });
});
</script>
@endpush
@endsection
