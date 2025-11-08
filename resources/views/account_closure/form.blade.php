@extends('layouts.app')

@section('title', 'Account Closure Form - SKI Capital')

@section('content')
<style>
    .survey_container { background: #fff; padding: 30px; border-radius: 8px; box-shadow: 0 0 10px rgba(0,0,0,0.1); margin-bottom: 20px; }
    .error { color: red; font-size: 12px; margin-top: 5px; }
    .data-list { list-style: none; padding: 0; }
    .data-list li { margin-bottom: 15px; }
    .data-list label { font-weight: 600; margin-bottom: 5px; display: block; }
    #bottom-wizard { margin-top: 30px; }
    .forward { padding: 10px 30px; }
    .warning-box {
        background: #fff3cd;
        border: 1px solid #ffc107;
        padding: 15px;
        border-radius: 5px;
        margin: 20px 0;
    }
    .warning-box h3 {
        color: #856404;
        font-size: 16px;
        margin-bottom: 10px;
    }
    .warning-box ul {
        margin: 10px 0;
        padding-left: 20px;
    }
</style>

<div id="top" class="container">
    <div class="row">
        <div class="col-lg-8">
            <section>
                <!-- Main Form -->
                <div id="survey_container" class="step_box step1 survey_container">
                    <form id="frmReasonForClosure" method="post" enctype="multipart/form-data">
                        @csrf
                        <div id="middle-wizard">
                            <div class="step">
                                <div class="row">
                                    <h3 class="col-md-12">Account Closure</h3>
                                    <p class="col-md-12"><strong>Account closure is permanent and irreversible.</strong></p>
                                    <div class="col-md-12">
                                        <ul class="data-list">
                                            <li>
                                                <label>Name</label>
                                                <input type="text" name="name" class="required form-control" placeholder="Name" required value="{{ old('name') }}">
                                                <div class="error"></div>
                                            </li>
                                            <li>
                                                <label>Email</label>
                                                <input type="email" name="email" class="required form-control" placeholder="Email" required value="{{ old('email') }}">
                                                <div class="error"></div>
                                            </li>
                                            <li>
                                                <label>DP ID</label>
                                                <input type="text" name="dp_id" class="required form-control" placeholder="DP ID" required value="{{ old('dp_id') }}">
                                                <div class="error"></div>
                                            </li>
                                            <li>
                                                <label>Client Master</label>
                                                <input type="file" name="client_master_file" class="form-control" accept=".pdf,.jpg,.jpeg,.png" value="">
                                                <small class="text-muted">Upload client master document (PDF, JPG, PNG)</small>
                                                <div id="client_master_file_error" class="error"></div>
                                            </li>
                                            <li>
                                                <label>Reason for Closure</label>
                                                <select class="form-control" name="reason_for_closure" id="reason_for_closure" required>
                                                    <option value="">Select Reason</option>
                                                    <option value="Financial Constraints" {{ old('reason_for_closure') == 'Financial Constraints' ? 'selected' : '' }}>Financial Constraints</option>
                                                    <option value="Service issue" {{ old('reason_for_closure') == 'Service issue' ? 'selected' : '' }}>Service issue</option>
                                                    <option value="Others" {{ old('reason_for_closure') == 'Others' ? 'selected' : '' }}>Others</option>
                                                </select>
                                                <div class="error"></div>
                                            </li>
                                            <li>
                                                <label>Mobile Number</label>
                                                <input type="number" name="mobile_number" id="mobile_number" class="required form-control" placeholder="Mobile Number" required value="{{ old('mobile_number') }}">
                                                <div class="error" id="mobile_number_error"></div>
                                            </li>
                                        </ul>
                                    </div>

                                    <div class="col-md-12">
                                        <h5 style="margin-top: 20px; margin-bottom: 15px;">TARGET ACC. DETAILS</h5>
                                        <ul class="data-list">
                                            <li>
                                                <label>DP ID</label>
                                                <input type="text" name="target_dp_id" class="required form-control" placeholder="Target DP ID" required value="{{ old('target_dp_id') }}">
                                                <div class="error"></div>
                                            </li>
                                            <li>
                                                <label>Client ID</label>
                                                <input type="text" name="client_id" class="required form-control" placeholder="Client ID" required value="{{ old('client_id') }}">
                                                <div class="error"></div>
                                            </li>
                                            <li>
                                                <label>Trading Code/UCC</label>
                                                <input type="text" name="trading_code" class="required form-control" placeholder="Trading Code/UCC" required value="{{ old('trading_code') }}">
                                                <div class="error"></div>
                                            </li>
                                        </ul>
                                    </div>

                                    <div class="col-md-12">
                                        <div class="warning-box">
                                            <h3>Please download your trade book, ledger, contract notes, tax P&L and any other statements before proceeding with account closure. You may need these for tax filing and compliance. This will not be available for download once the account is closed.</h3>
                                            <ul>
                                                <li>1. eSign and click on Sign now.</li>
                                                <li>2. Once you Accept the T&C, enter your Aadhaar number and click Send OTP.</li>
                                                <li>3. Enter the OTP received on your Aadhaar linked mobile number and click Verify OTP.</li>
                                            </ul>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div id="bottom-wizard">
                            <button type="submit" id="btnReasonForClosure" class="forward btn btn-primary">Submit</button>
                        </div>
                    </form>
                </div>

                <!-- OTP Verification Form -->
                <div id="survey_container_otp" style="display:none" class="step_box step1 survey_container">
                    <form id="frmReasonForClosure_otp" method="post">
                        @csrf
                        <div id="middle-wizard">
                            <div class="step">
                                <div class="row">
                                    <h3 class="col-md-12">Account Closure - OTP Verification</h3>
                                    <p class="col-md-12"><strong>Account closure is permanent and irreversible.</strong></p>
                                    <div class="col-md-12">
                                        <span style="color:green; font-weight: bold;" id="success_msg"></span>
                                        <ul class="data-list">
                                            <li>
                                                <label>OTP</label>
                                                <input type="number" id="otp" name="otp" class="required form-control" placeholder="OTP" required maxlength="4">
                                                <span class="error" id="otp_error"></span>
                                            </li>
                                        </ul>
                                    </div>
                                    <div class="col-md-12">
                                        <div class="warning-box">
                                            <h3>Please download your trade book, ledger, contract notes, tax P&L and any other statements before proceeding with account closure. You may need these for tax filing and compliance. This will not be available for download once the account is closed.</h3>
                                            <ul>
                                                <li>1. eSign and click on Sign now.</li>
                                                <li>2. Once you Accept the T&C, enter your Aadhaar number and click Send OTP.</li>
                                                <li>3. Enter the OTP received on your Aadhaar linked mobile number and click Verify OTP.</li>
                                            </ul>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div id="bottom-wizard">
                            <button type="button" class="forward btn btn-primary" id="otp_btn" onclick="account_closureUser_verify_otp_NEW()">Verify OTP</button>
                        </div>
                    </form>
                </div>
            </section>
        </div>
    </div>
</div>

@push('scripts')
<script src="https://cdnjs.cloudflare.com/ajax/libs/jquery-validate/1.19.3/jquery.validate.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script>
// OTP form enter key
function accountclosureuserotpFRM_NEW(evt) {
    if (evt.keyCode == 13)
        account_closureUser_verify_otp_NEW();
}

// Verify final OTP
function account_closureUser_verify_otp_NEW() {
    jQuery(".field_error").html("");
    jQuery("#otp_btn").html("Please wait...");
    jQuery("#otp_btn").attr("disabled", true);
    var otp = jQuery("#otp").val();

    jQuery.ajax({
        url: '{{ route("account-closure.verifyFinalOtp") }}',
        type: 'post',
        data: {
            otp: otp,
            _token: '{{ csrf_token() }}'
        },
        success: function(result) {
            if (result.success) {
                Swal.fire({
                    title: 'Success',
                    text: 'Account closure request submitted successfully',
                    icon: 'success',
                    confirmButtonText: 'OK'
                });
                setTimeout(function() {
                    window.location.href = '{{ url('/') }}';
                }, 2000);
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
    // Main form validation and submission
    $("#frmReasonForClosure").validate({
        rules: {
            name: {
                required: true,
                maxlength: 100
            },
            email: {
                required: true,
                email: true
            },
            dp_id: {
                required: true
            },
            reason_for_closure: {
                required: true
            },
            mobile_number: {
                required: true,
                number: true,
                minlength: 10,
                maxlength: 10
            },
            target_dp_id: {
                required: true
            },
            client_id: {
                required: true
            },
            trading_code: {
                required: true
            }
        },
        errorElement: "div",
        errorPlacement: function(error, element) {
            element.closest("li").find(".error").append(error);
        },
        submitHandler: function(form) {
            var formData = new FormData(form);

            jQuery("#btnReasonForClosure").html("Please wait...");
            jQuery("#btnReasonForClosure").attr("disabled", true);

            jQuery.ajax({
                url: '{{ route("account-closure.submit") }}',
                type: 'POST',
                data: formData,
                processData: false,
                contentType: false,
                success: function(result) {
                    if (result.success) {
                        // Show OTP form
                        $('#survey_container').hide();
                        $('#survey_container_otp').show();
                        $('#success_msg').html(result.message || 'OTP sent to your mobile number');
                    } else {
                        alert(result.message || 'Error submitting form');
                        jQuery("#btnReasonForClosure").html("Submit");
                        jQuery("#btnReasonForClosure").attr("disabled", false);
                    }
                },
                error: function(xhr) {
                    var errors = xhr.responseJSON?.errors;
                    if (errors) {
                        $.each(errors, function(key, value) {
                            $('[name="' + key + '"]').closest('li').find('.error').html(value[0]);
                        });
                    } else {
                        alert('Error submitting form. Please try again.');
                    }
                    jQuery("#btnReasonForClosure").html("Submit");
                    jQuery("#btnReasonForClosure").attr("disabled", false);
                }
            });
        }
    });

    // OTP input enter key
    $('#otp').on('keypress', function(e) {
        if (e.which == 13) {
            account_closureUser_verify_otp_NEW();
        }
    });
});
</script>
@endpush
@endsection
