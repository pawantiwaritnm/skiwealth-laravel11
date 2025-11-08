@extends('layouts.app')

@section('title', 'Document Upload - SKI Capital')

@section('content')
<style>
    .document-form { padding: 30px; background: #fff; border-radius: 8px; box-shadow: 0 0 10px rgba(0,0,0,0.1); }
    .error { color: red; font-size: 12px; margin-top: 5px; }
    .form-group label { font-weight: 600; }
    .required-mark { color: red; }
    .upload-note { background: #f8f9fa; padding: 15px; border-left: 4px solid #5b6b3d; margin: 20px 0; }
    .existing-file { margin-top: 10px; padding: 10px; background: #e9ecef; border-radius: 4px; }
    .btn-submit { padding: 10px 30px; margin: 20px 0; }
</style>

<div class="container mt-4">
    <div class="row">
        <div class="col-lg-8 mx-auto">
            <div class="document-form">
                <h3 class="mb-4">Upload KYC Documents</h3>

                <div class="upload-note">
                    <p><strong>Note:</strong></p>
                    <ul>
                        <li>All documents should be clear and readable</li>
                        <li>Supported formats: JPG, JPEG, PNG, PDF</li>
                        <li>Maximum file size: 5 MB per document</li>
                        <li>Please ensure all information in documents is visible</li>
                    </ul>
                </div>

                <form id="frmDocuments" method="post" enctype="multipart/form-data">
                    @csrf

                    <div class="row">
                        <!-- PAN Card -->
                        <div class="col-md-6">
                            <div class="form-group mb-3">
                                <label>PAN Card <span class="required-mark">*</span></label>
                                <input type="file" name="pan_card" class="form-control" accept=".jpg,.jpeg,.png,.pdf" {{ $kycDocument && $kycDocument->pan_card ? '' : 'required' }}>
                                <div class="error" id="pan_card_error"></div>
                                @if($kycDocument && $kycDocument->pan_card)
                                    <div class="existing-file">
                                        <small>Current file: <a href="{{ asset('storage/' . $kycDocument->pan_card) }}" target="_blank">View Document</a></small>
                                    </div>
                                @endif
                            </div>
                        </div>

                        <!-- Aadhaar Card -->
                        <div class="col-md-6">
                            <div class="form-group mb-3">
                                <label>Aadhaar Card <span class="required-mark">*</span></label>
                                <input type="file" name="aadhaar_card" class="form-control" accept=".jpg,.jpeg,.png,.pdf" {{ $kycDocument && $kycDocument->aadhaar_card ? '' : 'required' }}>
                                <div class="error" id="aadhaar_card_error"></div>
                                @if($kycDocument && $kycDocument->aadhaar_card)
                                    <div class="existing-file">
                                        <small>Current file: <a href="{{ asset('storage/' . $kycDocument->aadhaar_card) }}" target="_blank">View Document</a></small>
                                    </div>
                                @endif
                            </div>
                        </div>

                        <!-- Address Proof -->
                        <div class="col-md-6">
                            <div class="form-group mb-3">
                                <label>Address Proof <span class="required-mark">*</span></label>
                                <input type="file" name="address_proof" class="form-control" accept=".jpg,.jpeg,.png,.pdf" {{ $kycDocument && $kycDocument->address_proof ? '' : 'required' }}>
                                <small class="form-text text-muted">Bank Statement, Utility Bill, etc.</small>
                                <div class="error" id="address_proof_error"></div>
                                @if($kycDocument && $kycDocument->address_proof)
                                    <div class="existing-file">
                                        <small>Current file: <a href="{{ asset('storage/' . $kycDocument->address_proof) }}" target="_blank">View Document</a></small>
                                    </div>
                                @endif
                            </div>
                        </div>

                        <!-- Bank Proof -->
                        <div class="col-md-6">
                            <div class="form-group mb-3">
                                <label>Bank Proof <span class="required-mark">*</span></label>
                                <input type="file" name="bank_proof" class="form-control" accept=".jpg,.jpeg,.png,.pdf" {{ $kycDocument && $kycDocument->bank_proof ? '' : 'required' }}>
                                <small class="form-text text-muted">Bank Statement or Passbook</small>
                                <div class="error" id="bank_proof_error"></div>
                                @if($kycDocument && $kycDocument->bank_proof)
                                    <div class="existing-file">
                                        <small>Current file: <a href="{{ asset('storage/' . $kycDocument->bank_proof) }}" target="_blank">View Document</a></small>
                                    </div>
                                @endif
                            </div>
                        </div>

                        <!-- Signature -->
                        <div class="col-md-6">
                            <div class="form-group mb-3">
                                <label>Signature <span class="required-mark">*</span></label>
                                <input type="file" name="signature" class="form-control" accept=".jpg,.jpeg,.png,.pdf" {{ $kycDocument && $kycDocument->signature ? '' : 'required' }}>
                                <small class="form-text text-muted">White paper signature scan</small>
                                <div class="error" id="signature_error"></div>
                                @if($kycDocument && $kycDocument->signature)
                                    <div class="existing-file">
                                        <small>Current file: <a href="{{ asset('storage/' . $kycDocument->signature) }}" target="_blank">View Document</a></small>
                                    </div>
                                @endif
                            </div>
                        </div>

                        <!-- Photo -->
                        <div class="col-md-6">
                            <div class="form-group mb-3">
                                <label>Passport Size Photo <span class="required-mark">*</span></label>
                                <input type="file" name="photo" class="form-control" accept=".jpg,.jpeg,.png" {{ $kycDocument && $kycDocument->photo ? '' : 'required' }}>
                                <div class="error" id="photo_error"></div>
                                @if($kycDocument && $kycDocument->photo)
                                    <div class="existing-file">
                                        <small>Current file: <a href="{{ asset('storage/' . $kycDocument->photo) }}" target="_blank">View Document</a></small>
                                    </div>
                                @endif
                            </div>
                        </div>

                        <!-- Income Proof (Optional) -->
                        <div class="col-md-6">
                            <div class="form-group mb-3">
                                <label>Income Proof <small>(Optional)</small></label>
                                <input type="file" name="income_proof" class="form-control" accept=".jpg,.jpeg,.png,.pdf">
                                <small class="form-text text-muted">Salary Slip, ITR, etc.</small>
                                <div class="error" id="income_proof_error"></div>
                                @if($kycDocument && $kycDocument->income_proof)
                                    <div class="existing-file">
                                        <small>Current file: <a href="{{ asset('storage/' . $kycDocument->income_proof) }}" target="_blank">View Document</a></small>
                                    </div>
                                @endif
                            </div>
                        </div>

                        <!-- Cancelled Cheque (Optional) -->
                        <div class="col-md-6">
                            <div class="form-group mb-3">
                                <label>Cancelled Cheque <small>(Optional)</small></label>
                                <input type="file" name="cancelled_cheque" class="form-control" accept=".jpg,.jpeg,.png,.pdf">
                                <div class="error" id="cancelled_cheque_error"></div>
                                @if($kycDocument && $kycDocument->cancelled_cheque)
                                    <div class="existing-file">
                                        <small>Current file: <a href="{{ asset('storage/' . $kycDocument->cancelled_cheque) }}" target="_blank">View Document</a></small>
                                    </div>
                                @endif
                            </div>
                        </div>
                    </div>

                    <div class="row mt-4">
                        <div class="col-md-12">
                            <button type="submit" class="btn btn-primary btn-submit" id="btnUploadDocuments">
                                <span id="btnText">Upload Documents</span>
                                <span id="btnLoader" style="display:none;">
                                    <i class="fa fa-spinner fa-spin"></i> Uploading...
                                </span>
                            </button>
                            <a href="{{ route('kyc.nomination') }}" class="btn btn-secondary btn-submit">Back</a>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<script>
$(document).ready(function() {
    $('#frmDocuments').on('submit', function(e) {
        e.preventDefault();

        // Clear previous errors
        $('.error').text('');

        // Show loading state
        $('#btnText').hide();
        $('#btnLoader').show();
        $('#btnUploadDocuments').prop('disabled', true);

        var formData = new FormData(this);

        $.ajax({
            url: "{{ route('kyc.uploadDocuments') }}",
            type: 'POST',
            data: formData,
            processData: false,
            contentType: false,
            success: function(response) {
                if (response.success) {
                    alert(response.message);
                    if (response.redirect) {
                        window.location.href = response.redirect;
                    }
                } else {
                    alert(response.message || 'An error occurred');
                }
            },
            error: function(xhr) {
                if (xhr.status === 422) {
                    var errors = xhr.responseJSON.errors;
                    $.each(errors, function(key, value) {
                        $('#' + key + '_error').text(value[0]);
                    });
                } else {
                    alert('An error occurred. Please try again.');
                }
            },
            complete: function() {
                // Hide loading state
                $('#btnText').show();
                $('#btnLoader').hide();
                $('#btnUploadDocuments').prop('disabled', false);
            }
        });
    });
});
</script>
@endsection
