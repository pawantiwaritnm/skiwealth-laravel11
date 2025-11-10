@extends('layouts.app')

@section('title', 'KYC Form - SKI Capital')

@section('content')
<style>
    #verfyIfsc, #chngeIfsc {
        background: #fff0;
        color: #1f3000;
        border: 2px solid;
        padding: 5px 20px 6px 30px;
        font-weight: 600;
    }
    .hide_box { display: none; }
    .step_box { display: none; }
    .step_box.active { display: block; }
    .error { color: red; font-size: 12px; margin-top: 5px; }
    .survey_container { background: #fff; padding: 30px; border-radius: 8px; box-shadow: 0 0 10px rgba(0,0,0,0.1); }
    #bottom-wizard { margin-top: 30px; }
    .backward, .forward { padding: 10px 30px; margin: 0 5px; }
    .data-list { list-style: none; padding: 0; }
    .data-list li { margin-bottom: 15px; }
    .data-list label { font-weight: 600; margin-bottom: 5px; display: block; }
    .floated { display: flex; flex-wrap: wrap; gap: 15px; }
    .check_radio { margin-right: 10px; }
    .styled-select select { width: 100%; padding: 8px; border: 1px solid #ccc; border-radius: 4px; }
    .guardian_section { display: none; }
    .m10 { margin: 10px 0; }
    .m20 { margin: 20px 0; }
    .last_note { background: #f8f9fa; padding: 15px; border-left: 4px solid #5b6b3d; margin: 20px 0; }
</style>

<div id="top" class="container">
    <div class="row">
        <div class="col-lg-8">
            <section>
                <!-- Step 1: Personal Info -->
                <div id="survey_container" class="step_box active step1 survey_container">
                    <form id="frmPersonalInfo" method="post">
                        @csrf
                        <div id="middle-wizard">
                            <div class="step">
                                <div class="row">
                                    <h3 class="col-md-12">Personal Info</h3>
                                    <div class="col-md-12">
                                        <ul class="data-list">
                                            <li>
                                                <label>Father's Name <span style="font-size:12px;">(As mentioned in your PAN card)</span></label>
                                                <input type="text" name="father_name" class="required form-control" placeholder="Father's Name" value="{{ old('father_name', $personalInfo->father_name ?? '') }}" required>
                                                <div class="error"></div>
                                            </li>
                                            <li>
                                                <label>Mother's Name</label>
                                                <input type="text" name="mother_name" class="required form-control" placeholder="Mother's Name" value="{{ old('mother_name', $personalInfo->mother_name ?? '') }}" required>
                                                <div class="error"></div>
                                            </li>
                                            <li>
                                                <label>Date of Birth</label>
                                                <input type="date" name="dob" id="dob" class="required form-control"
                                                    value="{{ old('dob', isset($personalInfo->dob) ? \Carbon\Carbon::parse($personalInfo->dob)->format('Y-m-d') : '') }}"
                                                    required>
                                                    <div class="error"></div>
                                            </li>
                                        </ul>

                                        <label class="m10">Gender</label>
                                        <ul class="data-list floated clearfix m20">
                                            <li id="age"></li>
                                            <li>
                                                <input type="radio" name="gender" value="Male" class="check_radio option-input" id="male" {{ old('gender', $personalInfo->gender ?? '') == 'Male' ? 'checked' : '' }} required>
                                                <label for="male">Male</label>
                                            </li>
                                            <li>
                                                <input type="radio" name="gender" value="Female" class="check_radio option-input" id="female" {{ old('gender', $personalInfo->gender ?? '') == 'Female' ? 'checked' : '' }}>
                                                <label for="female">Female</label>
                                            </li>
                                            <li>
                                                <input type="radio" name="gender" value="Other" class="check_radio option-input" id="other" {{ old('gender', $personalInfo->gender ?? '') == 'Other' ? 'checked' : '' }}>
                                                <label for="other">Other</label>
                                            </li>
                                            <div class="error"></div>
                                        </ul>

                                        <label class="m10">Marital Status</label>
                                        <ul style="list-style-type:none;" class="data-list floated clearfix m20">
                                            <li id="age"></li>
                                            <li>
                                                <input type="radio" name="marital_status" value="Single" class="check_radio option-input" id="single" {{ old('marital_status', $personalInfo->marital_status ?? '') == 'Single' ? 'checked' : '' }} required>
                                                <label for="single">Single</label>
                                            </li>
                                            <li>
                                                <input type="radio" name="marital_status" value="Married" class="check_radio option-input" id="married" {{ old('marital_status', $personalInfo->marital_status ?? '') == 'Married' ? 'checked' : '' }}>
                                                <label for="married">Married</label>
                                            </li>
                                            <div class="error"></div>
                                        </ul>

                                        <ul class="data-list mt-2">
                                            <li>
                                                <label>Occupation</label>
                                                <div class="styled-select">
                                                    <select name="occupation" class="form-control" required>
                                                        <option value="">Select Occupation</option>
                                                        <option value="Service" {{ old('occupation', $personalInfo->occupation ?? '') == 'Service' ? 'selected' : '' }}>Service</option>
                                                        <option value="Business" {{ old('occupation', $personalInfo->occupation ?? '') == 'Business' ? 'selected' : '' }}>Business</option>
                                                        <option value="Professional" {{ old('occupation', $personalInfo->occupation ?? '') == 'Professional' ? 'selected' : '' }}>Professional</option>
                                                        <option value="Self Employed" {{ old('occupation', $personalInfo->occupation ?? '') == 'Self Employed' ? 'selected' : '' }}>Self Employed</option>
                                                        <option value="Retired" {{ old('occupation', $personalInfo->occupation ?? '') == 'Retired' ? 'selected' : '' }}>Retired</option>
                                                        <option value="Housewife" {{ old('occupation', $personalInfo->occupation ?? '') == 'Housewife' ? 'selected' : '' }}>Housewife</option>
                                                        <option value="Student" {{ old('occupation', $personalInfo->occupation ?? '') == 'Student' ? 'selected' : '' }}>Student</option>
                                                        <option value="Others" {{ old('occupation', $personalInfo->occupation ?? '') == 'Others' ? 'selected' : '' }}>Others</option>
                                                    </select>
                                                </div>
                                                <div style="margin-top: 25px;" class="error"></div>
                                            </li>
                                        </ul>

                                        <label class="m10 radio">Residential Status</label>
                                        <ul class="data-list floated clearfix m20">
                                            <li id="age"></li>
                                            <li>
                                                <input type="radio" name="residential_status" value="Resident Individual" class="check_radio option-input" id="resident" {{ old('residential_status', $personalInfo->residential_status ?? '') == 'Resident Individual' ? 'checked' : '' }} required>
                                                <label for="resident">Resident Individual</label>
                                            </li>
                                            <li>
                                                <input type="radio" name="residential_status" value="Non Resident Indian" class="check_radio option-input" id="nri" {{ old('residential_status', $personalInfo->residential_status ?? '') == 'Non Resident Indian' ? 'checked' : '' }}>
                                                <label for="nri">Non Resident Indian (NRI)</label>
                                            </li>
                                            <div class="error"></div>
                                        </ul>

                                        <ul class="data-list m10">
                                            <li>
                                                <label>Annual Income</label>
                                                <div class="styled-select">
                                                    <select name="annual_income" class="form-control" required>
                                                        <option value="">Select Annual Income</option>
                                                        <option value="Below 1 Lakh" {{ old('annual_income', $personalInfo->annual_income ?? '') == 'Below 1 Lakh' ? 'selected' : '' }}>Below 1 Lakh</option>
                                                        <option value="1-5 Lakh" {{ old('annual_income', $personalInfo->annual_income ?? '') == '1-5 Lakh' ? 'selected' : '' }}>1-5 Lakh</option>
                                                        <option value="5-10 Lakh" {{ old('annual_income', $personalInfo->annual_income ?? '') == '5-10 Lakh' ? 'selected' : '' }}>5-10 Lakh</option>
                                                        <option value="10-25 Lakh" {{ old('annual_income', $personalInfo->annual_income ?? '') == '10-25 Lakh' ? 'selected' : '' }}>10-25 Lakh</option>
                                                        <option value="Above 25 Lakh" {{ old('annual_income', $personalInfo->annual_income ?? '') == 'Above 25 Lakh' ? 'selected' : '' }}>Above 25 Lakh</option>
                                                    </select>
                                                </div>
                                                <div style="margin-top: 25px;" class="error"></div>
                                            </li>
                                        </ul>

                                        <ul class="data-list">
                                            <li>
                                                <label>PAN No</label>
                                                <input type="text" name="pan_no" class="required form-control" placeholder="PAN Number" value="{{ old('pan_no', $personalInfo->pan_no ?? '') }}" pattern="^([a-zA-Z]){5}([0-9]){4}([a-zA-Z]){1}?$" required>
                                                <div class="validation_message_error error" id="pan_noError"></div>
                                                <div class="validation_message_error" style="color:green" id="pan_noSuccess"></div>
                                            </li>
                                            <li>
                                                <label>Aadhaar Number</label>
                                                <input type="text" name="aadhaar_number" class="required form-control" placeholder="Aadhaar Number" value="{{ old('aadhaar_number', $personalInfo->aadhaar_number ?? '') }}" maxlength="12" pattern="[0-9]{12}">
                                                <div class="error" id="aadhaar_numberError"></div>
                                            </li>
                                        </ul>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div id="bottom-wizard">
                            <button type="submit" id="btnPersonalInfo" class="forward btn btn-primary">Next</button>
                        </div>
                    </form>
                </div>

                <!-- Step 2: Address -->
                <div id="survey_container1" class="step_box step2 survey_container">
                    <form id="frmAddress" method="post" role="form">
                        @csrf
                        <div id="middle-wizard">
                            <div class="step">
                                <div class="row">
                                    <h3 class="col-md-12">Contact Info</h3>
                                    <div class="col-md-12">
                                        <ul class="data-list">
                                            <li><label>Permanent Address</label></li>
                                        </ul>
                                        <ul class="data-list">
                                            <li>
                                                <label>Address 1</label>
                                                <textarea class="required form-control" required name="permanent_address1" placeholder="Address 1">{{ old('permanent_address1', $address->permanent_address1 ?? '') }}</textarea>
                                            </li>
                                        </ul>
                                        <ul class="data-list">
                                            <li>
                                                <label>Address 2</label>
                                                <textarea class="required form-control" name="permanent_address2" placeholder="Address 2">{{ old('permanent_address2', $address->permanent_address2 ?? '') }}</textarea>
                                            </li>
                                        </ul>
                                        <ul class="data-list">
                                            <li>
                                                <label>City</label>
                                                <input type="text" name="permanent_address_city" class="required form-control" required placeholder="City" value="{{ old('permanent_address_city', $address->permanent_address_city ?? '') }}">
                                            </li>
                                        </ul>
                                        <ul class="data-list">
                                            <li>
                                                <label>Country</label>
                                                <div class="styled-select">
                                                    <select name="permanent_address_country" class="form-control" required>
                                                        <option value="">Select Country</option>
                                                        @foreach($countries as $country)
                                                            <option value="{{ $country->id }}" {{ old('permanent_address_country', $address->permanent_address_country ?? 101) == $country->id ? 'selected' : '' }}>
                                                                {{ $country->country }}
                                                            </option>
                                                        @endforeach
                                                    </select>
                                                </div>
                                            </li>
                                            <div style="margin-top: 25px;" class="error"></div>
                                        </ul>
                                        <ul class="data-list">
                                            <label>Pin Code</label>
                                            <li>
                                                <input type="number" name="permanent_address_pincode" class="required form-control" placeholder="Pin Code" required value="{{ old('permanent_address_pincode', $address->permanent_address_pincode ?? '') }}">
                                            </li>
                                        </ul>
                                    </div>

                                    <div class="col-md-1 col-sm-1 col-xs-1">
                                        <ul class="data-list">
                                            <li>
                                                <input name="is_same" type="checkbox" class="check_radio option-input2" id="same_as_permanent" {{ old('is_same', $address->is_same ?? 0) == 1 ? 'checked' : '' }}>
                                            </li>
                                        </ul>
                                    </div>
                                    <div class="col-md-11 col-sm-11 col-xs-11">
                                        <ul class="data-list">
                                            <li><label>Click here if correspondence address not same as permanent address</label></li>
                                        </ul>
                                    </div>

                                    <div class="col-md-12 {{ old('is_same', $address->is_same ?? 0) == 1 ? '' : 'hide_box' }}" id="correspondence_address_box">
                                        <h3 class="">Correspondence Address</h3>
                                        <ul class="data-list">
                                            <li><label>Correspondence Address</label></li>
                                        </ul>
                                        <ul class="data-list">
                                            <li>
                                                <label>Address 1</label>
                                                <textarea class="required form-control" name="correspondence_address1" id="correspondence_address1" placeholder="Address 1">{{ old('correspondence_address1', $address->correspondence_address1 ?? '') }}</textarea>
                                            </li>
                                        </ul>
                                        <ul class="data-list">
                                            <li>
                                                <label>Address 2</label>
                                                <textarea class="required form-control" name="correspondence_address2" id="correspondence_address2" placeholder="Address 2">{{ old('correspondence_address2', $address->correspondence_address2 ?? '') }}</textarea>
                                            </li>
                                        </ul>
                                        <ul class="data-list">
                                            <li>
                                                <label>City</label>
                                                <input type="text" id="correspondence_address_city" name="correspondence_address_city" class="required form-control" placeholder="City" value="{{ old('correspondence_address_city', $address->correspondence_address_city ?? '') }}">
                                            </li>
                                        </ul>
                                        <ul class="data-list">
                                            <li>
                                                <label>Country</label>
                                                <div class="styled-select">
                                                    <select name="correspondence_address_country" class="form-control" required>
                                                        <option value="">Select Country</option>
                                                        @foreach($countries as $country)
                                                            <option value="{{ $country->id }}" {{ old('correspondence_address_country', $address->correspondence_address_country ?? 101) == $country->id ? 'selected' : '' }}>
                                                                {{ $country->country }}
                                                            </option>
                                                        @endforeach
                                                    </select>
                                                </div>
                                            </li>
                                            <div style="margin-top: 25px;" class="error"></div>
                                        </ul>
                                        <ul class="data-list">
                                            <label>Pin Code</label>
                                            <li>
                                                <input type="number" id="correspondence_address_pincode" name="correspondence_address_pincode" class="required form-control" placeholder="Pin Code" value="{{ old('correspondence_address_pincode', $address->correspondence_address_pincode ?? '') }}">
                                            </li>
                                        </ul>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div id="bottom-wizard">
                            <button type="button" name="forward" class="backward btn btn-secondary" onclick="step_change('1')">Back</button>
                            <button type="submit" id="btnAddress" class="forward btn btn-primary">Next</button>
                        </div>
                    </form>
                </div>

                <!-- Step 3: Bank Details -->
                <div id="survey_container2" class="step_box step3 survey_container">
                    <form id="frmBankDetails" method="post" role="form">
                        @csrf
                        <div id="middle-wizard">
                            <div class="step">
                                <div class="row">
                                    <h3 class="col-md-12">Bank Info</h3>
                                    <div class="col-md-12">
                                        <label class="">Account Type</label>
                                        <ul class="data-list floated clearfix">
                                            <li id="age"></li>
                                            <li>
                                                <input type="radio" name="account_type" value="Savings" class="check_radio option-input" id="savings" {{ old('account_type', $bankDetail->account_type ?? '') == 'Savings' ? 'checked' : '' }} required>
                                                <label for="savings">Savings</label>
                                            </li>
                                            <li>
                                                <input type="radio" name="account_type" value="Current" class="check_radio option-input" id="current" {{ old('account_type', $bankDetail->account_type ?? '') == 'Current' ? 'checked' : '' }}>
                                                <label for="current">Current</label>
                                            </li>
                                        </ul>
                                        <ul class="data-list">
                                            <li>
                                                <label>Account Number</label>
                                                <input type="number" name="account_number" id="account_number" class="required form-control" placeholder="Account Number" required value="{{ old('account_number', $bankDetail->account_number ?? '') }}" {{ !empty($bankDetail->account_number) ? 'readonly' : '' }}>
                                                <span class="validation_message_error error" id="account_numberError"></span>
                                            </li>
                                        </ul>
                                        <ul class="data-list">
                                            <li>
                                                <label>IFSC Code</label>
                                                <input type="text" name="ifsc_code" id="ifsc_code_i" class="required form-control" placeholder="IFSC Code" required value="{{ old('ifsc_code', $bankDetail->ifsc_code ?? '') }}" {{ !empty($bankDetail->ifsc_code) ? 'readonly' : '' }}>
                                                <span class="validation_message_error error" id="ifsc_codeError"></span>
                                            </li>
                                        </ul>
                                        <ul class="data-list">
                                            <h3 class="validation_message_error" style="color:red" id="bank_Error"></h3>
                                            <h3 class="validation_message_success" style="color:green" id="bank_success"></h3>
                                        </ul>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div id="bottom-wizard">
                            <button type="button" name="forward" class="backward btn btn-secondary" onclick="step_change('2')">Back</button>
                            <button type="submit" id="btnBankDetails" class="forward btn btn-primary">Next</button>
                        </div>
                    </form>
                </div>

                <!-- Step 4: Market Segments -->
                <div id="survey_container3" class="step_box step4 survey_container">
                    <form id="frmMarketSegments" method="post" role="form">
                        @csrf
                        <div id="middle-wizard">
                            <div class="step">
                                <div class="row">
                                    <div class="col-md-10">
                                        <h3>Market Segments</h3>
                                        <ul class="data-list-2">
                                            <li>
                                                <input name="cash" type="checkbox" class="required check_radio option-input2" value="1" id="cash" {{ old('cash', $marketSegments->cash ?? 1) == 1 ? 'checked' : '' }}>
                                                <label for="cash">Cash</label>
                                            </li>
                                            <li>
                                                <input name="futures_options" type="checkbox" class="required check_radio option-input2" value="1" id="futures_options" {{ old('futures_options', $marketSegments->futures_options ?? 1) == 1 ? 'checked' : '' }}>
                                                <label for="futures_options">Futures and Options</label>
                                            </li>
                                            <li>
                                                <input name="commodity" type="checkbox" class="required check_radio option-input2" value="1" id="commodity" {{ old('commodity', $marketSegments->commodity ?? 1) == 1 ? 'checked' : '' }}>
                                                <label for="commodity">MCX Commodity</label>
                                            </li>
                                            <li>
                                                <input name="currency" type="checkbox" class="required check_radio option-input2" value="1" id="currency" {{ old('currency', $marketSegments->currency ?? 1) == 1 ? 'checked' : '' }}>
                                                <label for="currency">Currency</label>
                                            </li>
                                            <li>
                                                <input name="mutual_fund" type="checkbox" class="required check_radio option-input2" value="1" id="mutual_fund" {{ old('mutual_fund', $marketSegments->mutual_fund ?? 1) == 1 ? 'checked' : '' }}>
                                                <label for="mutual_fund">Mutual Funds</label>
                                            </li>
                                        </ul>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div id="bottom-wizard">
                            <button type="button" name="forward" class="backward btn btn-secondary" onclick="step_change('3')">Back</button>
                            <button type="submit" name="forward" id="btnMarketSegments" class="forward btn btn-primary">Next</button>
                        </div>
                    </form>
                </div>

                <!-- Step 5: Regulatory Info -->
                <div id="survey_container4" class="step_box step5 survey_container">
                    <form id="frmRegulatoryInfo" method="post" role="form">
                        @csrf
                        <div id="middle-wizard">
                            <div class="step">
                                <div class="row">
                                    <h3 style="margin-left: -16px;" class="col-md-12">Regulatory Info</h3>
                                    <div class="row">
                                        <div class="col-md-12">
                                            <label class="">Number of years of Investment/Trading Experience</label>
                                            <ul class="data-list floated clearfix">
                                                <li id="age"></li>
                                                @foreach(['0-1 Years', '1-3 Years', '3-5 Years', 'Above 5 Years'] as $experience)
                                                <li>
                                                    <input type="radio" name="number_of_years_of_investment" value="{{ $experience }}" class="check_radio option-input" id="exp_{{ $loop->index }}" {{ old('number_of_years_of_investment', $regulatoryInfo->number_of_years_of_investment ?? '') == $experience ? 'checked' : '' }} required>
                                                    <label for="exp_{{ $loop->index }}">{{ $experience }}</label>
                                                </li>
                                                @endforeach
                                            </ul>

                                            <label class="">Whether a Politically Exposed Person (PEP)?</label>
                                            <ul class="data-list floated clearfix">
                                                <li id="age"></li>
                                                <li>
                                                    <input type="radio" name="pep" value="Yes" class="check_radio option-input" id="RadioYes" {{ old('pep', $regulatoryInfo->pep ?? '') == 'Yes' ? 'checked' : '' }} onclick="ShowHideDiv()" required>
                                                    <label for="RadioYes">Yes</label>
                                                </li>
                                                <li>
                                                    <input type="radio" name="pep" value="No" class="check_radio option-input" id="RadioNo" {{ old('pep', $regulatoryInfo->pep ?? '') == 'No' ? 'checked' : '' }} onclick="ShowHideDiv()">
                                                    <label for="RadioNo">No</label>
                                                </li>
                                            </ul>

                                            <div id="pepdatafield" style="display: {{ old('pep', $regulatoryInfo->pep ?? '') == 'Yes' ? 'block' : 'none' }};">
                                                <ul class="data-list">
                                                    <li>
                                                        <label>Name of PEP</label>
                                                        <input type="text" name="name_of_pep" value="{{ old('name_of_pep', $regulatoryInfo->name_of_pep ?? '') }}" id="name_of_pep" class="required form-control" placeholder="Enter the name of PEP">
                                                    </li>
                                                </ul>
                                                <ul class="data-list">
                                                    <li>
                                                        <label>Relation with PEP</label>
                                                        <input type="text" value="{{ old('relation_with_pep', $regulatoryInfo->relation_with_pep ?? '') }}" name="relation_with_pep" id="relation_with_pep" class="required form-control" placeholder="Enter the Relation with PEP">
                                                    </li>
                                                </ul>
                                            </div>

                                            <label class="">Any action taken by SEBI or any other regulator for violation of law in the last 3 years?</label>
                                            <ul class="data-list floated clearfix">
                                                <li id="age"></li>
                                                <li>
                                                    <input type="radio" name="any_action_by_sebi" value="Yes" class="check_radio option-input" id="RadioYes2" {{ old('any_action_by_sebi', $regulatoryInfo->any_action_by_sebi ?? '') == 'Yes' ? 'checked' : '' }} onclick="ShowHideDiv2()" required>
                                                    <label for="RadioYes2">Yes</label>
                                                </li>
                                                <li>
                                                    <input type="radio" name="any_action_by_sebi" value="No" class="check_radio option-input" id="RadioNo2" {{ old('any_action_by_sebi', $regulatoryInfo->any_action_by_sebi ?? '') == 'No' ? 'checked' : '' }} onclick="ShowHideDiv2()">
                                                    <label for="RadioNo2">No</label>
                                                </li>
                                            </ul>

                                            <div id="Action_detail" style="display: {{ old('any_action_by_sebi', $regulatoryInfo->any_action_by_sebi ?? '') == 'Yes' ? 'block' : 'none' }};">
                                                <ul class="data-list">
                                                    <li>
                                                        <label>Details of action</label>
                                                        <input type="text" value="{{ old('details_of_action', $regulatoryInfo->details_of_action ?? '') }}" id="details_of_action" name="details_of_action" class="required form-control" placeholder="Enter details">
                                                    </li>
                                                </ul>
                                            </div>

                                            <div class="col-md-12 col-lg-12 m10">
                                                <label class="">Whether dealing with any other stock-broker?</label>
                                                <ul class="data-list floated clearfix">
                                                    <li id="age"></li>
                                                    <li>
                                                        <input type="radio" name="dealing_with_other_stockbroker" value="Yes" class="check_radio option-input" id="dealingYes" {{ old('dealing_with_other_stockbroker', $regulatoryInfo->dealing_with_other_stockbroker ?? '') == 'Yes' ? 'checked' : '' }} required>
                                                        <label for="dealingYes">Yes</label>
                                                    </li>
                                                    <li>
                                                        <input type="radio" name="dealing_with_other_stockbroker" value="No" class="check_radio option-input" id="dealingNo" {{ old('dealing_with_other_stockbroker', $regulatoryInfo->dealing_with_other_stockbroker ?? '') == 'No' ? 'checked' : '' }}>
                                                        <label for="dealingNo">No</label>
                                                    </li>
                                                </ul>
                                            </div>

                                            <div class="col-md-12 col-lg-12 m10">
                                                <label class="">Any Dispute pending with such stock-broker</label>
                                                <ul class="data-list floated clearfix">
                                                    <li id="age"></li>
                                                    <li>
                                                        <input type="radio" name="any_dispute_with_stockbroker" value="Yes" class="check_radio option-input" id="RadioYes3" {{ old('any_dispute_with_stockbroker', $regulatoryInfo->any_dispute_with_stockbroker ?? '') == 'Yes' ? 'checked' : '' }} onclick="ShowHideDiv3()" required>
                                                        <label for="RadioYes3">Yes</label>
                                                    </li>
                                                    <li>
                                                        <input type="radio" name="any_dispute_with_stockbroker" value="No" class="check_radio option-input" id="RadioNo3" {{ old('any_dispute_with_stockbroker', $regulatoryInfo->any_dispute_with_stockbroker ?? '') == 'No' ? 'checked' : '' }} onclick="ShowHideDiv3()">
                                                        <label for="RadioNo3">No</label>
                                                    </li>
                                                </ul>
                                            </div>

                                            <div id="details_of_disputes_box2" style="display: {{ old('any_dispute_with_stockbroker', $regulatoryInfo->any_dispute_with_stockbroker ?? '') == 'Yes' ? 'block' : 'none' }};">
                                                <ul class="data-list">
                                                    <li>
                                                        <label>Disputes details</label>
                                                        <input type="text" name="dispute_with_stockbroker_details" id="dispute_with_stockbroker_details" class="required form-control" value="{{ old('dispute_with_stockbroker_details', $regulatoryInfo->dispute_with_stockbroker_details ?? '') }}" placeholder="Enter details">
                                                    </li>
                                                </ul>
                                            </div>

                                            <div class="col-md-6" style="width:100%;">
                                                <label class="">Commodity trade classification (Select other if not a Farmer Producer Organizations or Value Chain)</label>
                                                <ul class="data-list floated clearfix">
                                                    <li id="age"></li>
                                                    @foreach(['Farmer Producer Organizations', 'Value Chain', 'Other'] as $classification)
                                                    <li>
                                                        <input type="radio" name="commodity_trade_classification" value="{{ $classification }}" class="check_radio option-input" id="ctc_{{ $loop->index }}" {{ old('commodity_trade_classification', $regulatoryInfo->commodity_trade_classification ?? '') == $classification ? 'checked' : '' }} required>
                                                        <label for="ctc_{{ $loop->index }}">{{ $classification }}</label>
                                                    </li>
                                                    @endforeach
                                                </ul>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div id="bottom-wizard">
                            <button type="button" name="forward" class="backward btn btn-secondary" onclick="step_change('4')">Back</button>
                            <button type="submit" name="forward" id="btnRegulatoryInfo" class="forward btn btn-primary">Next</button>
                        </div>
                    </form>
                </div>

                <!-- Step 6: Disclosures -->
                <div id="survey_container5" class="step_box step6 survey_container">
                    <form id="frmDisclosures" method="post" role="form">
                        @csrf
                        <div id="middle-wizard">
                            <div class="step">
                                <div class="row">
                                    <div class="col-md-12">
                                        <h3>Disclosures</h3>
                                        <h5>I have read and understood the following documents</h5>
                                        <ul class="data-list-2 m20">
                                            <li>
                                                <input type="checkbox" required class="required check_radio option-input2" id="doc1">
                                                <label for="doc1"><a href="{{ asset('pdf/Right_and_Obligation_Merged.pdf') }}" target="_blank">Rights and Obligations document</a></label>
                                            </li>
                                            <li>
                                                <input type="checkbox" required class="required check_radio option-input2" id="doc2">
                                                <label for="doc2"><a href="{{ asset('pdf/GUIDANCE_NOTE_SKI.pdf') }}" target="_blank">Risk Disclosure Document</a></label>
                                            </li>
                                            <li>
                                                <input type="checkbox" required class="required check_radio option-input2" id="doc3">
                                                <label for="doc3"><a href="{{ asset('pdf/Policies_and_Procedures_merged.pdf') }}" target="_blank">Policy and procedure</a></label>
                                            </li>
                                            <li>
                                                <input type="checkbox" required class="required check_radio option-input2" id="doc4">
                                                <label for="doc4"><a href="{{ asset('pdf/Tariff_Sheet_merged.pdf') }}" target="_blank">Tariff Sheet</a></label>
                                            </li>
                                        </ul>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div id="bottom-wizard">
                            <button type="button" name="forward" class="backward btn btn-secondary" onclick="step_change('5')">Back</button>
                            <button type="submit" name="forward" id="btnDisclosures" class="forward btn btn-primary">Next</button>
                        </div>
                    </form>
                </div>

                <!-- Step 7: Success Message (shown after nomination) -->
                <div id="survey_container6" class="step_box step7 survey_container">
                    <div id="middle-wizard">
                        <div class="step">
                            <div style="padding:0;" class="submit step" id="complete">
                                <i class="icon-check"></i>
                                <h3>Thank you message.</h3>
                                <p><strong><center>Congratulations!</center></strong></p>
                                <p>You have successfully submitted your trading and demat account application and the same is under process.</p>
                                <p><strong>Application number:</strong> {{ session('application_number') }}</p>
                                <p><strong>Date:</strong> {{ date('d-m-Y') }}</p>
                                <p>Our representative will contact you shortly with your account and login credentials.</p>
                                <p>Feel free to contact us should you have any questions or require any assistance regarding your account.</p>
                                <p>If you have your KYC* documents available at this point, you may upload the same by <span><a href="{{ route('kyc.documents') }}" style="color: #5b6b3d; font-weight: 600; text-decoration: underline;">clicking here</a></span></p>
                                <p>Alternatively, you may email the KYC documents to us on ekyc@skicapital.net or whatsapp on <a href="https://api.whatsapp.com/send?phone=9910785149&text=Hi,%20I%20need%20help%20regarding%20something" style="color: #5b6b3d; font-weight: 600; text-decoration: underline;" class="applyinfo-btn">9910785149</a></p>
                            </div>
                        </div>
                    </div>
                </div>
            </section>
        </div>

        <!-- Right Side Panel - SKI Edge -->
        <div class="col-lg-4 mt-4">
            <div class="row">
                <div class="col-md-12">
                    <h3 class="text-center">SKI Edge</h3>
                </div>
                <div class="col-md-3">
                    <div class="signup__body-wrapper-ad-panel-details-image">
                        <img src="{{ asset('images/skiRightimg1.png') }}" class="img-responsive skiimgrightside" alt="30 Years of Expertise" width="50" height="50">
                    </div>
                </div>
                <div class="col-md-9">
                    <div class="signup__body-wrapper-ad-panel-details">
                        <div class="signup__body-wrapper-ad-panel-details-info">
                            <div class="signup__body-wrapper-ad-panel-details-info-title">30 Years of Expertise</div>
                            <div class="signup__body-wrapper-ad-panel-details-info-text">Our financial advisory teams have been providing unparalleled services for more than 3 decades</div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="row">
                <div class="col-md-3">
                    <div class="signup__body-wrapper-ad-panel-details-image">
                        <img src="{{ asset('images/skiRightimg2.png') }}" class="img-responsive skiimgrightside" alt="Personal Service" width="50" height="50">
                    </div>
                </div>
                <div class="col-md-9">
                    <div class="signup__body-wrapper-ad-panel-details">
                        <div class="signup__body-wrapper-ad-panel-details-info">
                            <div class="signup__body-wrapper-ad-panel-details-info-title">A high level of personal service</div>
                            <div class="signup__body-wrapper-ad-panel-details-info-text">Your Relationship Manager will work with you to create a bespoke strategy, tailored to your objectives</div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="row">
                <div class="col-md-3">
                    <div class="signup__body-wrapper-ad-panel-details-image">
                        <img src="{{ asset('images/skiRightimg3.png') }}" class="img-responsive skiimgrightside" alt="Complete Financial Products" width="50" height="50">
                    </div>
                </div>
                <div class="col-md-9">
                    <div class="signup__body-wrapper-ad-panel-details">
                        <div class="signup__body-wrapper-ad-panel-details-info">
                            <div class="signup__body-wrapper-ad-panel-details-info-title">Complete Financial Products and Services</div>
                            <div class="signup__body-wrapper-ad-panel-details-info-text">Offering all the financial and insurance products to meet your diverse portfolio needs</div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

@push('scripts')
<script src="https://cdnjs.cloudflare.com/ajax/libs/jquery-validate/1.19.3/jquery.validate.min.js"></script>
<script>
// Global functions
function onlyNumberKey(evt) {
    var asciiCode = (evt.which) ? evt.which : evt.keyCode;
    if (asciiCode > 47 && asciiCode < 58)
        return true;
    return false;
}

// Step change function
function step_change(step) {
    $('.step_box').removeClass('active');
    $('.step' + step).addClass('active');
}

// Show/Hide conditional fields
function ShowHideDiv() {
    var RadioYes = document.getElementById("RadioYes");
    var pepdatafield = document.getElementById("pepdatafield");
    pepdatafield.style.display = RadioYes.checked ? "block" : "none";
    if (RadioYes.checked) {
        jQuery('#name_of_pep,#relation_with_pep').attr('required', '');
    } else {
        jQuery('#name_of_pep,#relation_with_pep').removeAttr('required');
        jQuery('#name_of_pep').val('');
        jQuery('#relation_with_pep').val('');
    }
}

function ShowHideDiv2() {
    var RadioYes2 = document.getElementById("RadioYes2");
    var Action_detail = document.getElementById("Action_detail");
    Action_detail.style.display = RadioYes2.checked ? "block" : "none";
    if (RadioYes2.checked) {
        jQuery('#details_of_action').attr('required', '');
    } else {
        jQuery('#details_of_action').removeAttr('required');
        jQuery('#details_of_action').val('');
    }
}

function ShowHideDiv3() {
    var RadioYes3 = document.getElementById("RadioYes3");
    var Action_detail = document.getElementById("details_of_disputes_box2");
    details_of_disputes_box2.style.display = RadioYes3.checked ? "block" : "none";
    if (RadioYes3.checked) {
        jQuery('#dispute_with_stockbroker_details').attr('required', '');
    } else {
        jQuery('#dispute_with_stockbroker_details').removeAttr('required');
        jQuery('#dispute_with_stockbroker_details').val('');
    }
}

// Form submission handlers
$(document).ready(function() {
    // Helper function to handle button states
    function setButtonLoading($btn, loading) {
        if (loading) {
            $btn.data('original-text', $btn.html());
            $btn.prop('disabled', true).html('<i class="fa fa-spinner fa-spin"></i> Processing...');
        } else {
            $btn.prop('disabled', false).html($btn.data('original-text') || 'Next');
        }
    }

    // Personal Info form
    $('#frmPersonalInfo').on('submit', function(e) {
        e.preventDefault();

        // Clear previous errors
        $('.error').text('');

        // Disable submit button and show processing
        var $btn = $('#btnPersonalInfo');
        setButtonLoading($btn, true);

        $.ajax({
            url: '{{ route("kyc.personalInfo") }}',
            method: 'POST',
            data: $(this).serialize(),
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            },
            success: function(response) {
                if (response.success) {
                    step_change('2');
                } else {
                    alert(response.message || 'An error occurred');
                }
            },
            error: function(xhr) {
                if (xhr.status === 422) {
                    var errors = xhr.responseJSON.errors;
                    $.each(errors, function(key, value) {
                        // Try multiple error element patterns
                        var errorElement = $('#' + key + 'Error');
                        if (errorElement.length === 0) {
                            errorElement = $('[name="' + key + '"]').closest('li').find('.error');
                        }
                        if (errorElement.length === 0) {
                            errorElement = $('[name="' + key + '"]').siblings('.error');
                        }
                        if (errorElement.length === 0) {
                            errorElement = $('[name="' + key + '"]').parent().find('.error');
                        }
                        errorElement.text(value[0]);
                    });
                } else {
                    alert('An error occurred. Please try again.');
                }
            },
            complete: function() {
                // Re-enable button
                setButtonLoading($btn, false);
            }
        });
    });

    // Address form
    $('#frmAddress').on('submit', function(e) {
        e.preventDefault();

        // Clear previous errors
        $('.error').text('');

        // Disable submit button and show processing
        var $btn = $('#btnAddress');
        setButtonLoading($btn, true);

        $.ajax({
            url: '{{ route("kyc.address") }}',
            method: 'POST',
            data: $(this).serialize(),
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            },
            success: function(response) {
                if (response.success) {
                    step_change('3');
                } else {
                    alert(response.message || 'An error occurred');
                }
            },
            error: function(xhr) {
                if (xhr.status === 422) {
                    var errors = xhr.responseJSON.errors;
                    $.each(errors, function(key, value) {
                        var errorElement = $('#' + key + 'Error');
                        if (errorElement.length === 0) {
                            errorElement = $('[name="' + key + '"]').closest('li').find('.error');
                        }
                        if (errorElement.length === 0) {
                            errorElement = $('[name="' + key + '"]').siblings('.error');
                        }
                        if (errorElement.length === 0) {
                            errorElement = $('[name="' + key + '"]').parent().find('.error');
                        }
                        errorElement.text(value[0]);
                    });
                } else {
                    alert('An error occurred. Please try again.');
                }
            },
            complete: function() {
                // Re-enable button
                setButtonLoading($btn, false);
            }
        });
    });

    // Bank Details form
    $('#frmBankDetails').on('submit', function(e) {
        e.preventDefault();

        // Clear previous errors
        $('.error').text('');

        var $btn = $('#btnBankDetails');
        setButtonLoading($btn, true);

        $.ajax({
            url: '{{ route("kyc.bankDetails") }}',
            method: 'POST',
            data: $(this).serialize(),
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            },
            success: function(response) {
                if (response.success) {
                    step_change('4');
                } else {
                    alert(response.message || 'An error occurred');
                }
            },
            error: function(xhr) {
                if (xhr.status === 422) {
                    var errors = xhr.responseJSON.errors;
                    $.each(errors, function(key, value) {
                        var errorElement = $('#' + key + 'Error');
                        if (errorElement.length === 0) {
                            errorElement = $('[name="' + key + '"]').closest('li').find('.error');
                        }
                        if (errorElement.length === 0) {
                            errorElement = $('[name="' + key + '"]').siblings('.error');
                        }
                        if (errorElement.length === 0) {
                            errorElement = $('[name="' + key + '"]').parent().find('.error');
                        }
                        errorElement.text(value[0]);
                    });
                } else {
                    alert('An error occurred. Please try again.');
                }
            },
            complete: function() {
                setButtonLoading($btn, false);
            }
        });
    });

    // Market Segments form
    $('#frmMarketSegments').on('submit', function(e) {
        e.preventDefault();

        // Clear previous errors
        $('.error').text('');

        var $btn = $('#btnMarketSegments');
        setButtonLoading($btn, true);

        $.ajax({
            url: '{{ route("kyc.marketSegments") }}',
            method: 'POST',
            data: $(this).serialize(),
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            },
            success: function(response) {
                if (response.success) {
                    step_change('5');
                } else {
                    alert(response.message || 'An error occurred');
                }
            },
            error: function(xhr) {
                if (xhr.status === 422) {
                    var errors = xhr.responseJSON.errors;
                    $.each(errors, function(key, value) {
                        var errorElement = $('#' + key + 'Error');
                        if (errorElement.length === 0) {
                            errorElement = $('[name="' + key + '"]').closest('li').find('.error');
                        }
                        if (errorElement.length === 0) {
                            errorElement = $('[name="' + key + '"]').siblings('.error');
                        }
                        if (errorElement.length === 0) {
                            errorElement = $('[name="' + key + '"]').parent().find('.error');
                        }
                        errorElement.text(value[0]);
                    });
                } else {
                    alert('An error occurred. Please try again.');
                }
            },
            complete: function() {
                setButtonLoading($btn, false);
            }
        });
    });

    // Regulatory Info form
    $('#frmRegulatoryInfo').on('submit', function(e) {
        e.preventDefault();

        // Clear previous errors
        $('.error').text('');

        var $btn = $('#btnRegulatoryInfo');
        setButtonLoading($btn, true);

        $.ajax({
            url: '{{ route("kyc.regulatoryInfo") }}',
            method: 'POST',
            data: $(this).serialize(),
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            },
            success: function(response) {
                if (response.success) {
                    step_change('6');
                } else {
                    alert(response.message || 'An error occurred');
                }
            },
            error: function(xhr) {
                if (xhr.status === 422) {
                    var errors = xhr.responseJSON.errors;
                    $.each(errors, function(key, value) {
                        var errorElement = $('#' + key + 'Error');
                        if (errorElement.length === 0) {
                            errorElement = $('[name="' + key + '"]').closest('li').find('.error');
                        }
                        if (errorElement.length === 0) {
                            errorElement = $('[name="' + key + '"]').siblings('.error');
                        }
                        if (errorElement.length === 0) {
                            errorElement = $('[name="' + key + '"]').parent().find('.error');
                        }
                        errorElement.text(value[0]);
                    });
                } else {
                    alert('An error occurred. Please try again.');
                }
            },
            complete: function() {
                setButtonLoading($btn, false);
            }
        });
    });

    // Disclosures form
    $('#frmDisclosures').on('submit', function(e) {
        e.preventDefault();

        // Clear previous errors
        $('.error').text('');

        var $btn = $('#btnDisclosures');
        setButtonLoading($btn, true);

        $.ajax({
            url: '{{ route("kyc.disclosures") }}',
            method: 'POST',
            data: $(this).serialize(),
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            },
            success: function(response) {
                if (response.success) {
                    // Redirect to nomination page
                    window.location.href = '{{ route("kyc.nomination") }}';
                } else {
                    alert(response.message || 'An error occurred');
                }
            },
            error: function(xhr) {
                if (xhr.status === 422) {
                    var errors = xhr.responseJSON.errors;
                    $.each(errors, function(key, value) {
                        var errorElement = $('#' + key + 'Error');
                        if (errorElement.length === 0) {
                            errorElement = $('[name="' + key + '"]').closest('li').find('.error');
                        }
                        if (errorElement.length === 0) {
                            errorElement = $('[name="' + key + '"]').siblings('.error');
                        }
                        if (errorElement.length === 0) {
                            errorElement = $('[name="' + key + '"]').parent().find('.error');
                        }
                        errorElement.text(value[0]);
                    });
                } else {
                    alert('An error occurred. Please try again.');
                }
            },
            complete: function() {
                setButtonLoading($btn, false);
            }
        });
    });

    // Same address checkbox
    $('#same_as_permanent').on('change', function() {
        if ($(this).is(':checked')) {
            $('#correspondence_address_box').removeClass('hide_box');
        } else {
            $('#correspondence_address_box').addClass('hide_box');
        }
    });
});
</script>
@endpush
@endsection
