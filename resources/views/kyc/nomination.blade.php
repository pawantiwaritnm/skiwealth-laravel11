@extends('layouts.app')

@section('title', 'Nomination Form - SKI Capital')

@section('content')
<style>
    .nomination-form { padding: 30px; background: #fff; border-radius: 8px; box-shadow: 0 0 10px rgba(0,0,0,0.1); }
    .guardian_section { display: none; }
    .last_note { background: #f8f9fa; padding: 15px; border-left: 4px solid #5b6b3d; margin: 20px 0; }
    .error { color: red; font-size: 12px; margin-top: 5px; display: block; }
    .text-danger { color: red; font-size: 12px; margin-top: 5px; display: block; }
    .is-invalid { border-color: red; }
    .check_radio { margin-right: 10px; }
    #bottom-wizard { margin-top: 30px; }
    .backward, .forward { padding: 10px 30px; margin: 0 5px; }
</style>

<div class="container">
    <div class="nomination-form">
        <form id="nomination_form" class="nomination_form" method="post" enctype="multipart/form-data" role="form">
            @csrf
            <input type="hidden" name="nomination_form_id" value="{{ $nomination->id ?? '' }}">

            <!-- General Error Display -->
            <div id="general_error" class="alert alert-danger" style="display: none; margin-bottom: 20px;"></div>

            <div class="row">
                <div class="col-md-12">
                    <h3 style="display: table;width: 100%;">Nomination Form</h3>
                </div>
            </div>

            <div class="row">
                <table class="table text-center table-bordered">
                    <tr>
                        <td>
                            <h5>SKI CAPITAL SERVICES LTD.<br>718, DR JOSHI ROAD<br>KAROL BAGH, NEW DELHI-110005</h5>
                        </td>
                        <td>
                            <h5>FORM FOR NOMINATION</h5>
                            <i>(To be filled in by individual applying singly or jointly)</i>
                        </td>
                    </tr>
                </table>
            </div>

            <div class="row">
                <div class="col-md-12">
                    <p>I/We wish to make a nomination [As per details given below].</p>
                    <h5>Nomination Details</h5>
                    <p>I/We wish to make a nomination and do hereby nominate the following person(s) who shall receive all the assets held in my / our account in the event.</p>
                </div>
            </div>

            @if(empty($nominationDetails) || count($nominationDetails) == 0)
                <!-- First Nominee Template -->
                <div class="nominee_Details" id="row1">
                    <div class="row">
                        <div class="col-md-12">
                            <h4 style="display: table;width: 100%;">Nominee 1 Details</h4>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-4">
                            <div class="form-group">
                                <label for="name_of_nominee_0">Name of Nominee(s) (Mr./Ms.)</label>
                                <input type="text" class="form-control" placeholder="" id="name_of_nominee_0" name="name_of_nominee[]" required>
                                <div class="error"></div>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="form-group">
                                <label for="nominee_mobile_0">Mobile</label>
                                <input type="text" class="form-control" placeholder="" id="nominee_mobile_0" name="nominee_mobile[]" maxlength="10" required>
                                <div class="error"></div>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="form-group">
                                <label for="nominee_email_0">Email ID</label>
                                <input type="email" class="form-control" placeholder="" id="nominee_email_0" name="nominee_email[]" required>
                                <div class="error"></div>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="form-group input-group">
                                <label for="share_of_nominees_0" style="margin-bottom: 25px;">Share of Nominee(s)</label>
                                <input type="number" class="form-control nominee_share" name="share_of_nominees[]" id="share_of_nominees_0" value="100">
                                <div class="input-group-append">
                                    <span class="input-group-text">%</span>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="form-group">
                                <label for="relation_applicant_name_nominees_0">Relation With the Applicant (If Any) Nominee(s) 1st</label>
                                <input type="text" class="form-control" placeholder="" id="relation_applicant_name_nominees_0" name="relation_applicant_name_nominees[]">
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-4">
                            <div class="form-group">
                                <label for="nominee_address_0">Address</label>
                                <input type="text" class="form-control" placeholder="" id="nominee_address_0" name="nominee_address[]" required>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="form-group">
                                <label for="nominee_city_0">City/Place</label>
                                <input type="text" class="form-control" placeholder="" id="nominee_city_0" name="nominee_city[]" required>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="form-group">
                                <label for="nominee_state_0">State</label>
                                <input type="text" class="form-control" placeholder="" id="nominee_state_0" name="nominee_state[]" required>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="form-group">
                                <label for="country_0">Country</label>
                                <select name="nominee_country[]" id="country_0" class="form-control" required>
                                    <option value="">Select Country</option>
                                    @foreach($countries as $country)
                                        <option value="{{ $country->id }}" {{ $country->id == 101 ? 'selected' : '' }}>{{ $country->country }}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="form-group">
                                <label for="nominee_pin_code_0">Pin Code</label>
                                <input type="text" class="form-control" placeholder="" id="nominee_pin_code_0" name="nominee_pin_code[]" maxlength="6" required>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="form-group">
                                <label for="nominee_dob_0">Date of Birth</label>
                                <input type="date" class="form-control" placeholder="" id="nominee_dob_0" name="nominee_dob[]" required>
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-12">
                            <h6 style="display: table;width: 100%;">Nominee Identification Details</h6>
                            <span>[Please tick any one of following and provide details of same]</span>
                            <div class="form-group tick_box" style="margin-top: 15px;">
                                <input type="radio" id="Photograph1" class="check_radio option-input" name="nominee_identification_0" value="photograph" required>
                                <label for="Photograph1" class="label_gender">Photograph & Signature</label><br>
                                <input type="radio" id="pan1" class="check_radio option-input" name="nominee_identification_0" value="pan">
                                <label for="pan1" class="label_gender">PAN</label><br>
                                <input type="radio" id="Aadhaar1" class="check_radio option-input" name="nominee_identification_0" value="aadhaar">
                                <label for="Aadhaar1" class="label_gender">Aadhaar</label><br>
                                <input type="radio" id="bank1" class="check_radio option-input" name="nominee_identification_0" value="saving_bank_account_no">
                                <label for="bank1" class="label_gender">Saving Bank Account No.</label><br>
                                <input type="radio" id="identity1" class="check_radio option-input" name="nominee_identification_0" value="proof_of_identity">
                                <label for="identity1" class="label_gender">Proof of Identity</label><br>
                                <input type="radio" id="demat1" class="check_radio option-input" name="nominee_identification_0" value="demat_account_iD">
                                <label for="demat1" class="label_gender">Demat Account ID</label>
                                <div class="nominee_first_identification error"></div>
                            </div>
                            <input type="hidden" name="radio_index[]" value="0">
                        </div>

                        <div class="col-md-6">
                            <h6 style="display: table;width: 100%;">Please Upload Selected Nominee Identification Document</h6>
                            <div class="form-group">
                                <label>Document</label>
                                <input type="file" class="form-control" placeholder="" name="nominee_document_0" required>
                                <div class="nominee_document error"></div>
                            </div>
                        </div>
                    </div>
                </div>
            @else
                <!-- Existing Nominees -->
                @foreach($nominationDetails as $index => $detail)
                <div class="nomination_form_existing">
                    <input type="hidden" name="nomination_details_id[]" value="{{ $detail->id }}">
                    <div class="nominee_Details" id="row{{ $index + 1 }}">
                        <div class="row">
                            <div class="col-md-12">
                                <h4 style="display: table;width: 100%;">Nominee(s) {{ $index + 1 }} Details</h4>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-4">
                                <div class="form-group">
                                    <label for="name_of_nominee_{{ $index }}">Name of Nominee(s) (Mr./Ms.)</label>
                                    <input type="text" class="form-control" name="name_of_nominee[]" id="name_of_nominee_{{ $index }}" value="{{ $detail->name_of_nominee }}" required>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="form-group">
                                    <label for="nominee_mobile_{{ $index }}">Mobile</label>
                                    <input type="text" class="form-control" id="nominee_mobile_{{ $index }}" name="nominee_mobile[]" value="{{ $detail->nominee_mobile }}" maxlength="10" required>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="form-group">
                                    <label for="nominee_email_{{ $index }}">Email ID</label>
                                    <input type="email" class="form-control" id="nominee_email_{{ $index }}" name="nominee_email[]" value="{{ $detail->nominee_email }}" required>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="form-group input-group">
                                    <label style="margin-bottom: 25px;">Share of Nominee(s)</label>
                                    <input type="number" class="form-control" value="{{ $detail->share_of_nominees }}" name="share_of_nominees[]">
                                    <div class="input-group-append">
                                        <span class="input-group-text">%</span>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="form-group">
                                    <label>Relation With the Applicant (If Any) Nominee(s)</label>
                                    <input type="text" class="form-control" value="{{ $detail->relation_applicant_name_nominees }}" name="relation_applicant_name_nominees[]">
                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-4">
                                <div class="form-group">
                                    <label for="nominee_address_{{ $index }}">Address</label>
                                    <input type="text" class="form-control" id="nominee_address_{{ $index }}" name="nominee_address[]" value="{{ $detail->nominee_address }}" required>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="form-group">
                                    <label for="nominee_city_{{ $index }}">City/Place</label>
                                    <input type="text" class="form-control" id="nominee_city_{{ $index }}" name="nominee_city[]" value="{{ $detail->nominee_city }}" required>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="form-group">
                                    <label for="nominee_state_{{ $index }}">State</label>
                                    <input type="text" class="form-control" id="nominee_state_{{ $index }}" name="nominee_state[]" value="{{ $detail->nominee_state }}" required>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="form-group">
                                    <label for="country_{{ $index }}">Country</label>
                                    <select name="nominee_country[]" id="country_{{ $index }}" class="form-control" required>
                                        <option value="">Select Country</option>
                                        @foreach($countries as $country)
                                            <option value="{{ $country->id }}" {{ $detail->nominees_country == $country->id ? 'selected' : '' }}>{{ $country->country }}</option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="form-group">
                                    <label for="nominee_pin_code_{{ $index }}">Pin Code</label>
                                    <input type="text" class="form-control" id="nominee_pin_code_{{ $index }}" name="nominee_pin_code[]" value="{{ $detail->nominee_pin_code }}" maxlength="6" required>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="form-group">
                                    <label for="nominee_dob_{{ $index }}">Date of Birth</label>
                                    <input type="date" class="form-control" id="nominee_dob_{{ $index }}" name="nominee_dob[]" value="{{ $detail->nominee_dob ? \Carbon\Carbon::parse($detail->nominee_dob)->format('Y-m-d') : '' }}" required>
                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-12">
                                <h6 style="display: table;width: 100%;">Nominee Identification Details</h6>
                                <span>[Please tick any one of following and provide details of same]</span>
                                <div class="form-group tick_box" style="margin-top: 15px;">
                                    <input type="radio" class="check_radio option-input" name="nominee_identification_{{ $index }}" value="photograph" {{ $detail->nominee_identification == 'photograph' ? 'checked' : '' }} required>
                                    <label class="label_gender">Photograph & Signature</label><br>
                                    <input type="radio" class="check_radio option-input" name="nominee_identification_{{ $index }}" value="pan" {{ $detail->nominee_identification == 'pan' ? 'checked' : '' }}>
                                    <label class="label_gender">PAN</label><br>
                                    <input type="radio" class="check_radio option-input" name="nominee_identification_{{ $index }}" value="aadhaar" {{ $detail->nominee_identification == 'aadhaar' ? 'checked' : '' }}>
                                    <label class="label_gender">Aadhaar</label><br>
                                    <input type="radio" class="check_radio option-input" name="nominee_identification_{{ $index }}" value="saving_bank_account_no" {{ $detail->nominee_identification == 'saving_bank_account_no' ? 'checked' : '' }}>
                                    <label class="label_gender">Saving Bank Account No.</label><br>
                                    <input type="radio" class="check_radio option-input" name="nominee_identification_{{ $index }}" value="proof_of_identity" {{ $detail->nominee_identification == 'proof_of_identity' ? 'checked' : '' }}>
                                    <label class="label_gender">Proof of Identity</label><br>
                                    <input type="radio" class="check_radio option-input" name="nominee_identification_{{ $index }}" value="demat_account_iD" {{ $detail->nominee_identification == 'demat_account_iD' ? 'checked' : '' }}>
                                    <label class="label_gender">Demat Account ID</label>
                                </div>
                                <input type="hidden" name="radio_index[]" value="{{ $index }}">
                            </div>
                            <div class="col-md-6">
                                <h6 style="display: table;width: 100%;">Please Upload Selected Nominee Identification Document</h6>
                                <div class="form-group">
                                    <label>Document</label>
                                    <input type="file" class="form-control" name="nominee_document_{{ $index }}">
                                    <input type="hidden" name="nominee_document_{{ $index }}_old" value="{{ $detail->nominee_document }}">
                                    @if(!empty($detail->nominee_document))
                                        @php
                                            $ext = pathinfo($detail->nominee_document, PATHINFO_EXTENSION);
                                        @endphp
                                        @if($ext == 'pdf')
                                            <a target='_blank' href="{{ asset('storage/' . $detail->nominee_document) }}">
                                                <img src="{{ asset('images/pdficon.png') }}" />
                                            </a><br /><br />
                                        @else
                                            <a target='_blank' href="{{ asset('storage/' . $detail->nominee_document) }}">
                                                <img width="50" height="60" src="{{ asset('storage/' . $detail->nominee_document) }}" />
                                            </a><br /><br />
                                        @endif
                                    @endif
                                </div>
                            </div>
                        </div>
                        @if($index > 0)
                        <div class="row">
                            <div class="col-md-12">
                                <button type="button" name="remove" class="btn btn-danger btn_remove" data-id="{{ $detail->id }}">Remove</button>
                            </div>
                        </div>
                        @endif
                    </div>
                </div>
                @endforeach
            @endif

            <div id="nominee_field"></div>
            <div class="row" style="margin-top: 20px; margin-bottom: 20px;">
                <div class="col-md-12">
                    <button type="button" name="add_nominee" id="add_nominee" class="btn btn-success">
                        <i class="fa fa-plus"></i> Add More Nominee
                    </button>
                </div>
            </div>

            <!-- Guardian Section -->
            <div class="row">
                <div class="col-md-12">
                    <br>
                    <h6>if Nominee(s) is a minor:</h6>
                </div>
                <div class="col-md-6">
                    <div class="form-group">
                        <input type="radio" class="check_radio option-input" id="nominee_minor_yes" name="nominee_minor" value="1" {{ ($nomination->nominee_minor ?? 0) ? 'checked' : '' }} required>
                        <label for="nominee_minor_yes" class="label_gender">Yes</label><br>
                        <input type="radio" class="check_radio option-input" id="nominee_minor_no" name="nominee_minor" value="0" {{ ($nomination->nominee_minor ?? 0) ? '' : 'checked' }}>
                        <label for="nominee_minor_no" class="label_gender">No</label>
                        <div class="error"></div>
                    </div>
                </div>
            </div>

            <div class="row guardian_section" style="display: {{ ($nomination->nominee_minor ?? 0) ? 'flex' : 'none' }};">
                <div class="col-md-6">
                    <div class="form-group">
                        <label>Name of Guardian (Mr./Ms.) {in case of minor Nominee(s)}</label>
                        <input type="text" class="form-control" name="guardian_name" value="{{ $nomination->guardian_name ?? '' }}">
                        <div class="error"></div>
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="form-group">
                        <label>Mobile</label>
                        <input type="text" class="form-control" id="guardian_mobile" name="guardian_mobile" maxlength="10" value="{{ $nomination->guardian_mobile ?? '' }}">
                        <div class="error"></div>
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="form-group">
                        <label>Email ID</label>
                        <input type="email" class="form-control" id="guardian_email" name="guardian_email" value="{{ $nomination->guardian_email ?? '' }}">
                        <div class="error"></div>
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="form-group">
                        <label>Relation of Guardian with Nominee</label>
                        <input type="text" class="form-control" name="relation_of_guardian" value="{{ $nomination->relation_of_guardian ?? '' }}">
                        <div class="error"></div>
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="form-group">
                        <label>Date of Birth {in case of minor Nominee(s)}</label>
                        <input type="date" class="form-control" name="date_of_birth" value="{{ isset($nomination->date_of_birth) && $nomination->date_of_birth ? \Carbon\Carbon::parse($nomination->date_of_birth)->format('Y-m-d') : '' }}">
                        <div class="error"></div>
                    </div>
                </div>
            </div>

            <div class="row guardian_section" style="display: {{ ($nomination->nominee_minor ?? 0) ? 'flex' : 'none' }};">
                <div class="col-md-12">
                    <h6>Address of Guardian(s)</h6>
                </div>
                <div class="col-md-4">
                    <div class="form-group">
                        <label>Address</label>
                        <input type="text" class="form-control" name="guardian_address" value="{{ $nomination->guardian_address ?? '' }}">
                        <div class="error"></div>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="form-group">
                        <label>City/Place</label>
                        <input type="text" class="form-control" name="guardian_city" value="{{ $nomination->guardian_city ?? '' }}">
                        <div class="error"></div>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="form-group">
                        <label>State</label>
                        <input type="text" class="form-control" name="guardian_state" value="{{ $nomination->guardian_state ?? '' }}">
                        <div class="error"></div>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="form-group">
                        <label>Country</label>
                        <select name="guardian_country" class="form-control">
                            <option value="">Select Country</option>
                            @foreach($countries as $country)
                                <option value="{{ $country->id }}" {{ ($nomination->guardian_country ?? 101) == $country->id ? 'selected' : '' }}>{{ $country->country }}</option>
                            @endforeach
                        </select>
                        <div class="error"></div>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="form-group">
                        <label>Pin Code</label>
                        <input type="text" class="form-control" id="guardian_pin_code" name="guardian_pin_code" value="{{ $nomination->guardian_pin_code ?? '' }}" maxlength="6">
                        <div class="error"></div>
                    </div>
                </div>
            </div>

            <div class="row guardian_section" style="display: {{ ($nomination->nominee_minor ?? 0) ? 'flex' : 'none' }};">
                <div class="col-md-12">
                    <h6>Guardian Identification Details</h6>
                    <span>[Please tick any one of following and provide details of same]</span>
                    <div class="form-group tick_box" style="margin-top: 15px;">
                        <input type="radio" class="check_radio option-input" id="Photograph" name="guardian_identification" value="photograph" {{ ($nomination->guardian_identification ?? '') == 'photograph' ? 'checked' : '' }}>
                        <label for="Photograph" class="label_gender">Photograph & Signature</label><br>
                        <input type="radio" class="check_radio option-input" id="pan" name="guardian_identification" value="pan" {{ ($nomination->guardian_identification ?? '') == 'pan' ? 'checked' : '' }}>
                        <label for="pan" class="label_gender">PAN</label><br>
                        <input type="radio" class="check_radio option-input" id="Aadhaar" name="guardian_identification" value="aadhaar" {{ ($nomination->guardian_identification ?? '') == 'aadhaar' ? 'checked' : '' }}>
                        <label for="Aadhaar" class="label_gender">Aadhaar</label><br>
                        <input type="radio" class="check_radio option-input" id="bank" name="guardian_identification" value="saving_bank_account_no" {{ ($nomination->guardian_identification ?? '') == 'saving_bank_account_no' ? 'checked' : '' }}>
                        <label for="bank" class="label_gender">Saving Bank Account No.</label><br>
                        <input type="radio" class="check_radio option-input" id="identity" name="guardian_identification" value="proof_of_identity" {{ ($nomination->guardian_identification ?? '') == 'proof_of_identity' ? 'checked' : '' }}>
                        <label for="identity" class="label_gender">Proof of Identity</label><br>
                        <input type="radio" class="check_radio option-input" id="demat" name="guardian_identification" value="demat_account_iD" {{ ($nomination->guardian_identification ?? '') == 'demat_account_iD' ? 'checked' : '' }}>
                        <label for="demat" class="label_gender">Demat Account ID</label>
                    </div>
                </div>
                <div class="col-md-6">
                    <h6 style="display: table;width: 100%;">Please Upload Selected Guardian Identification Document</h6>
                    <div class="form-group">
                        <label>Document</label>
                        <input type="file" class="form-control {{ !empty($nomination->guardian_document) ? 'guardian_document_exist' : '' }}" name="guardian_document">
                        <input type="hidden" name="guardian_document_name_old" value="{{ $nomination->guardian_document ?? '' }}" />
                        <div class="error"></div>
                        @if(!empty($nomination->guardian_document))
                            @php
                                $ext = pathinfo($nomination->guardian_document, PATHINFO_EXTENSION);
                            @endphp
                            @if($ext == 'pdf')
                                <a target='_blank' href="{{ asset('storage/' . $nomination->guardian_document) }}">
                                    <img src="{{ asset('images/pdficon.png') }}" />
                                </a><br /><br />
                            @else
                                <a target='_blank' href="{{ asset('storage/' . $nomination->guardian_document) }}">
                                    <img width="50" height="60" src="{{ asset('storage/' . $nomination->guardian_document) }}" />
                                </a><br /><br />
                            @endif
                        @endif
                    </div>
                </div>
            </div>

            <div class="last_note">
                <P style="margin-bottom: 5px;"><strong>Note*</strong></P>
                <p>This nomination shall supersede any prior nomination by the account holder(s), if any,<br>The Trading Member / Depository Participant shall provide acknowledgement of the nomination form to the account holder(s)</p>
            </div>

            <div id="bottom-wizard">
                <button type="button" name="forward" class="backward btn btn-secondary" onclick="window.location.href='{{ route('kyc.form') }}'">Back</button>
                <button type="submit" name="forward" id="nominationform" class="forward btn btn-primary">Next</button>
            </div>
        </form>
    </div>
</div>

@push('scripts')
<script src="https://cdnjs.cloudflare.com/ajax/libs/jquery-validate/1.19.5/jquery.validate.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/jquery-validate/1.19.5/additional-methods.min.js"></script>
<script>
$(document).ready(function() {
    var nomineeCount = {{ count($nominationDetails ?? []) > 0 ? count($nominationDetails) : 1 }};

    // Get countries list for dynamic forms
    var countriesOptions = `<option value="">Select Country</option>@foreach($countries as $country)<option value="{{ $country->id }}" {{ $country->id == 101 ? 'selected' : '' }}>{{ $country->country }}</option>@endforeach`;

    // Custom validation method for total share
    $.validator.addMethod("totalShare", function(value, element) {
        var total = 0;
        $('.nominee_share').each(function() {
            var val = parseFloat($(this).val()) || 0;
            total += val;
        });
        return total === 100;
    }, "Total share of all nominees must equal 100%");

    // Setup jQuery Validation with proper settings
    $.validator.setDefaults({
        errorElement: 'span',
        errorClass: 'error text-danger',
        errorPlacement: function(error, element) {
            // Place error message below the input field
            if (element.parent('.input-group').length) {
                error.insertAfter(element.parent());
            } else if (element.is(':radio')) {
                error.insertAfter(element.closest('.tick_box'));
            } else {
                error.insertAfter(element);
            }
        },
        highlight: function(element) {
            $(element).addClass('is-invalid');
        },
        unhighlight: function(element) {
            $(element).removeClass('is-invalid');
        }
    });

    // Client-side validation function
    function validateForm() {
        var isValid = true;
        var errors = [];

        // Validate nominee mobile numbers
        $('input[name="nominee_mobile[]"]').each(function(index) {
            var value = $(this).val().trim();
            if (value === '') {
                errors.push('Nominee ' + (index + 1) + ' mobile is required');
                $(this).addClass('is-invalid').siblings('.error').text('Mobile is required');
                isValid = false;
            } else if (!/^[0-9]{10}$/.test(value)) {
                errors.push('Nominee ' + (index + 1) + ' mobile must be exactly 10 digits');
                $(this).addClass('is-invalid').siblings('.error').text('Must be exactly 10 digits');
                isValid = false;
            }
        });

        // Validate nominee pin codes
        $('input[name="nominee_pin_code[]"]').each(function(index) {
            var value = $(this).val().trim();
            if (value === '') {
                errors.push('Nominee ' + (index + 1) + ' pin code is required');
                $(this).addClass('is-invalid').siblings('.error').text('Pin code is required');
                isValid = false;
            } else if (!/^[0-9]{6}$/.test(value)) {
                errors.push('Nominee ' + (index + 1) + ' pin code must be exactly 6 digits');
                $(this).addClass('is-invalid').siblings('.error').text('Must be exactly 6 digits');
                isValid = false;
            }
        });

        // Validate guardian fields if minor is selected
        if ($('input[name="nominee_minor"]:checked').val() == '1') {
            var guardianMobile = $('#guardian_mobile').val().trim();
            if (guardianMobile === '') {
                errors.push('Guardian mobile is required');
                $('#guardian_mobile').addClass('is-invalid').siblings('.error').text('Mobile is required');
                isValid = false;
            } else if (!/^[0-9]{10}$/.test(guardianMobile)) {
                errors.push('Guardian mobile must be exactly 10 digits');
                $('#guardian_mobile').addClass('is-invalid').siblings('.error').text('Must be exactly 10 digits');
                isValid = false;
            }

            var guardianPin = $('#guardian_pin_code').val().trim();
            if (guardianPin === '') {
                errors.push('Guardian pin code is required');
                $('#guardian_pin_code').addClass('is-invalid').siblings('.error').text('Pin code is required');
                isValid = false;
            } else if (!/^[0-9]{6}$/.test(guardianPin)) {
                errors.push('Guardian pin code must be exactly 6 digits');
                $('#guardian_pin_code').addClass('is-invalid').siblings('.error').text('Must be exactly 6 digits');
                isValid = false;
            }
        }

        if (!isValid) {
            var errorHtml = '<strong>Please fix the following errors:</strong><ul style="margin-top: 10px; margin-bottom: 0;">';
            errors.forEach(function(error) {
                errorHtml += '<li>' + error + '</li>';
            });
            errorHtml += '</ul>';
            $('#general_error').html(errorHtml).show();
            $('html, body').animate({ scrollTop: 0 }, 'slow');
        }

        return isValid;
    }

    // Form submission handler
    $('#nomination_form').on('submit', function(e) {
        e.preventDefault(); // Prevent default form submission and page reload
        e.stopPropagation(); // Stop event bubbling

        // Clear previous errors
        $('#general_error').hide().html('');
        $('.error').text('');
        $('.is-invalid').removeClass('is-invalid');

        // Run client-side validation
        if (!validateForm()) {
            return false;
        }

        // Disable submit button
        var $btn = $('#nominationform');
        var originalText = $btn.html();
        $btn.prop('disabled', true).html('<i class="fa fa-spinner fa-spin"></i> Processing...');

        var formData = new FormData(this);

        $.ajax({
            url: '{{ route("kyc.nomination.submit") }}',
            method: 'POST',
            data: formData,
            processData: false,
            contentType: false,
            success: function(response) {
                if (response.success) {
                    if (response.redirect) {
                        window.location.href = response.redirect;
                    } else {
                        window.location.href = '{{ route("kyc.documents") }}';
                    }
                } else {
                    $('#general_error').html('<strong>Error:</strong> ' + response.message).show();
                    $('html, body').animate({ scrollTop: 0 }, 'slow');
                    $btn.prop('disabled', false).html(originalText);
                }
            },
            error: function(xhr) {
                if (xhr.status === 422) {
                    var errors = xhr.responseJSON.errors;
                    var errorHtml = '<strong>Please fix the following errors:</strong><ul style="margin-top: 10px; margin-bottom: 0;">';
                    var firstErrorField = null;

                    $.each(errors, function(key, value) {
                        errorHtml += '<li>' + value[0] + '</li>';

                        // Try to find the field and show inline error
                        var fieldName = key.replace(/\./g, '_').replace(/\*/g, '');
                        var $field = $('[name="' + key + '"]');

                        // For array fields like nominee_mobile.0
                        if ($field.length === 0 && key.includes('.')) {
                            var parts = key.split('.');
                            var baseName = parts[0];
                            var index = parts[1];
                            $field = $('[name="' + baseName + '[]"]').eq(parseInt(index));
                        }

                        if ($field.length > 0) {
                            $field.addClass('is-invalid');
                            var $errorContainer = $field.siblings('.error').first();
                            if ($errorContainer.length === 0) {
                                $errorContainer = $field.parent().find('.error').first();
                            }
                            if ($errorContainer.length === 0) {
                                $errorContainer = $field.closest('.form-group').find('.error').first();
                            }
                            if ($errorContainer.length > 0) {
                                $errorContainer.text(value[0]).show();
                            }

                            // Track first error field for scrolling
                            if (!firstErrorField) {
                                firstErrorField = $field;
                            }
                        }
                    });

                    errorHtml += '</ul>';
                    $('#general_error').html(errorHtml).show();

                    // Scroll to first error or top
                    if (firstErrorField) {
                        $('html, body').animate({
                            scrollTop: firstErrorField.offset().top - 100
                        }, 'slow');
                    } else {
                        $('html, body').animate({ scrollTop: 0 }, 'slow');
                    }
                } else if (xhr.responseJSON && xhr.responseJSON.message) {
                    $('#general_error').html('<strong>Error:</strong> ' + xhr.responseJSON.message).show();
                    $('html, body').animate({ scrollTop: 0 }, 'slow');
                } else {
                    $('#general_error').html('<strong>Error:</strong> An unexpected error occurred. Please try again.').show();
                    $('html, body').animate({ scrollTop: 0 }, 'slow');
                }
                $btn.prop('disabled', false).html(originalText);
            }
        });

        return false; // Extra safeguard to prevent form submission
    });

    // Toggle guardian section
    $('input[name="nominee_minor"]').on('change', function() {
        if ($(this).val() == '1') {
            $('.guardian_section').show();
            $('.guardian_section input, .guardian_section select').attr('required', 'required');
        } else {
            $('.guardian_section').hide();
            $('.guardian_section input, .guardian_section select').removeAttr('required');
        }
    });

    // Add more nominees
    $('#add_nominee').on('click', function() {
        nomineeCount++;
        var html = `
            <div class="nominee_Details" id="row${nomineeCount}" style="border: 2px solid #e0e0e0; padding: 20px; margin-bottom: 20px; border-radius: 8px; background-color: #f9f9f9;">
                <div class="row">
                    <div class="col-md-12">
                        <h4 style="display: table; width: 100%; margin-bottom: 20px; color: #5b6b3d; border-bottom: 2px solid #5b6b3d; padding-bottom: 10px;">
                            Nominee ${nomineeCount} Details
                        </h4>
                    </div>
                </div>
                <div class="row">
                    <div class="col-md-4">
                        <div class="form-group">
                            <label>Name of Nominee(s) (Mr./Ms.) <span class="text-danger">*</span></label>
                            <input type="text" class="form-control" name="name_of_nominee[]" required>
                            <div class="error"></div>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="form-group">
                            <label>Mobile <span class="text-danger">*</span></label>
                            <input type="text" class="form-control" name="nominee_mobile[]" maxlength="10" required>
                            <div class="error"></div>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="form-group">
                            <label>Email ID <span class="text-danger">*</span></label>
                            <input type="email" class="form-control" name="nominee_email[]" required>
                            <div class="error"></div>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="form-group">
                            <label>Share of Nominee(s) <span class="text-danger">*</span></label>
                            <div class="input-group">
                                <input type="number" class="form-control nominee_share" name="share_of_nominees[]" value="0" step="0.01" min="0" max="100" required>
                                <div class="input-group-append">
                                    <span class="input-group-text">%</span>
                                </div>
                            </div>
                            <div class="error"></div>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="form-group">
                            <label>Relation With the Applicant</label>
                            <input type="text" class="form-control" name="relation_applicant_name_nominees[]">
                            <div class="error"></div>
                        </div>
                    </div>
                </div>
                <div class="row">
                    <div class="col-md-4">
                        <div class="form-group">
                            <label>Address <span class="text-danger">*</span></label>
                            <input type="text" class="form-control" name="nominee_address[]" required>
                            <div class="error"></div>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="form-group">
                            <label>City/Place <span class="text-danger">*</span></label>
                            <input type="text" class="form-control" name="nominee_city[]" required>
                            <div class="error"></div>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="form-group">
                            <label>State <span class="text-danger">*</span></label>
                            <input type="text" class="form-control" name="nominee_state[]" required>
                            <div class="error"></div>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="form-group">
                            <label>Country <span class="text-danger">*</span></label>
                            <select name="nominee_country[]" class="form-control" required>
                                ${countriesOptions}
                            </select>
                            <div class="error"></div>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="form-group">
                            <label>Pin Code <span class="text-danger">*</span></label>
                            <input type="text" class="form-control" name="nominee_pin_code[]" maxlength="6" required>
                            <div class="error"></div>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="form-group">
                            <label>Date of Birth <span class="text-danger">*</span></label>
                            <input type="date" class="form-control" name="nominee_dob[]" required>
                            <div class="error"></div>
                        </div>
                    </div>
                </div>
                <div class="row">
                    <div class="col-md-12">
                        <h6 style="display: table; width: 100%; margin-top: 10px;">Nominee Identification Details</h6>
                        <span>[Please tick any one of following and provide details of same]</span>
                        <div class="form-group tick_box" style="margin-top: 15px;">
                            <input type="radio" id="photo_${nomineeCount}" class="check_radio option-input" name="nominee_identification_${nomineeCount-1}" value="photograph" required>
                            <label for="photo_${nomineeCount}" class="label_gender">Photograph & Signature</label><br>
                            <input type="radio" id="pan_${nomineeCount}" class="check_radio option-input" name="nominee_identification_${nomineeCount-1}" value="pan">
                            <label for="pan_${nomineeCount}" class="label_gender">PAN</label><br>
                            <input type="radio" id="aadhaar_${nomineeCount}" class="check_radio option-input" name="nominee_identification_${nomineeCount-1}" value="aadhaar">
                            <label for="aadhaar_${nomineeCount}" class="label_gender">Aadhaar</label><br>
                            <input type="radio" id="bank_${nomineeCount}" class="check_radio option-input" name="nominee_identification_${nomineeCount-1}" value="saving_bank_account_no">
                            <label for="bank_${nomineeCount}" class="label_gender">Saving Bank Account No.</label><br>
                            <input type="radio" id="identity_${nomineeCount}" class="check_radio option-input" name="nominee_identification_${nomineeCount-1}" value="proof_of_identity">
                            <label for="identity_${nomineeCount}" class="label_gender">Proof of Identity</label><br>
                            <input type="radio" id="demat_${nomineeCount}" class="check_radio option-input" name="nominee_identification_${nomineeCount-1}" value="demat_account_iD">
                            <label for="demat_${nomineeCount}" class="label_gender">Demat Account ID</label>
                            <div class="error"></div>
                        </div>
                        <input type="hidden" name="radio_index[]" value="${nomineeCount-1}">
                    </div>
                    <div class="col-md-6">
                        <h6 style="display: table; width: 100%;">Please Upload Selected Nominee Identification Document</h6>
                        <div class="form-group">
                            <label>Document <span class="text-danger">*</span></label>
                            <input type="file" class="form-control" name="nominee_document_${nomineeCount-1}" required>
                            <div class="error"></div>
                        </div>
                    </div>
                </div>
                <div class="row" style="margin-top: 15px;">
                    <div class="col-md-12">
                        <button type="button" class="btn btn-danger btn_remove_dynamic">
                            <i class="fa fa-trash"></i> Remove This Nominee
                        </button>
                    </div>
                </div>
            </div>
        `;
        $('#nominee_field').append(html);
    });

    // Remove dynamic nominee
    $(document).on('click', '.btn_remove_dynamic', function() {
        if (confirm('Are you sure you want to remove this nominee?')) {
            $(this).closest('.nominee_Details').remove();
            nomineeCount--;
        }
    });

    // Remove existing nominee
    $('.btn_remove').on('click', function() {
        var id = $(this).data('id');
        if (confirm('Are you sure you want to remove this nominee?')) {
            $.ajax({
                url: '{{ route("kyc.nomination.remove") }}',
                method: 'POST',
                data: {
                    nominee_id: id,
                    _token: '{{ csrf_token() }}'
                },
                success: function(response) {
                    if (response.success) {
                        location.reload();
                    } else {
                        alert(response.message || 'Failed to remove nominee');
                    }
                },
                error: function() {
                    alert('An error occurred while removing the nominee');
                }
            });
        }
    });

    // Auto-calculate share percentages
    $(document).on('blur', '.nominee_share', function() {
        var total = 0;
        $('.nominee_share').each(function() {
            var val = parseFloat($(this).val()) || 0;
            total += val;
        });

        if (total !== 100) {
            $('#general_error').html('<strong>Warning:</strong> Total share of all nominees is ' + total + '%. It must equal 100%.').show();
        } else {
            $('#general_error').hide();
        }
    });

    // Restrict mobile fields to only digits and max 10 characters
    $(document).on('input', 'input[name="nominee_mobile[]"], #guardian_mobile', function() {
        this.value = this.value.replace(/[^0-9]/g, '').substring(0, 10);
    });

    // Restrict pin code fields to only digits and max 6 characters
    $(document).on('input', 'input[name="nominee_pin_code[]"], #guardian_pin_code', function() {
        this.value = this.value.replace(/[^0-9]/g, '').substring(0, 6);
    });
});
</script>
@endpush
@endsection
