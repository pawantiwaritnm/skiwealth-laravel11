<?php

namespace App\Http\Controllers\KYC;

use App\Http\Controllers\Controller;
use App\Models\Registration;
use App\Models\PersonalDetail;
use App\Models\Address;
use App\Models\BankDetail;
use App\Models\MarketSegment;
use App\Models\RegulatoryInfo;
use App\Models\KycDocument;
use App\Models\Nomination;
use App\Models\NominationDetail;
use App\Services\SandboxApiService;
use App\Services\BankVerificationService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class KycController extends Controller
{
    protected SandboxApiService $sandboxApi;
    protected BankVerificationService $bankService;

    public function __construct(
        SandboxApiService $sandboxApi,
        BankVerificationService $bankService
    ) {
        $this->sandboxApi = $sandboxApi;
        $this->bankService = $bankService;
    }

    /**
     * Show KYC form.
     */
    public function showForm(Request $request)
    {
        $userId = session('user_id');
        $registration = Registration::with([
            'personalDetail',
            'address',
            'bankDetail',
            'marketSegment',
            'regulatoryInfo',
            'kycDocument',
            'nomination.nominationDetails'
        ])->findOrFail($userId);

        // Get all countries for dropdown
        $countries = \App\Models\Country::where('status', 1)->orderBy('country')->get();

        // Extract individual data for easier access in views
        $personalInfo = $registration->personalDetail;
        $address = $registration->address;
        $bankDetail = $registration->bankDetail;
        $marketSegment = $registration->marketSegment;
        $regulatoryInfo = $registration->regulatoryInfo;
        $kycDocument = $registration->kycDocument;
        $nomination = $registration->nomination;

        return view('kyc.form', compact(
            'registration',
            'countries',
            'personalInfo',
            'address',
            'bankDetail',
            'marketSegment',
            'regulatoryInfo',
            'kycDocument',
            'nomination'
        ));
    }

    /**
     * Step 1: Submit Personal Information.
     */
    public function submitPersonalInfo(Request $request)
    {   
        $validator = Validator::make($request->all(), [
            'father_name' => 'required|string|max:255',
            'mother_name' => 'required|string|max:255',
            'dob' => 'required|date|before:today',
            'gender' => 'required|in:Male,Female,Other',
            'occupation' => 'required|string|max:100',
            'marital_status' => 'required|in:Single,Married,Divorced,Widowed',
            'pan_no' => 'required|string|size:10|regex:/^[A-Z]{5}[0-9]{4}[A-Z]{1}$/',
            'aadhaar_number' => 'required|digits:12',
            'residential_status' => 'required|string|max:50',
            'annual_income' => 'required|string|max:50',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'errors' => $validator->errors(),
            ], 422);
        }

        $userId = session('user_id');
        $registration = Registration::findOrFail($userId);

        try {
            // Verify PAN with Sandbox API
            $panVerification = $this->sandboxApi->verifyPan($request->pan_no);
             //dd($panVerification);
            // if (!$panVerification['success']) {
            //     return response()->json([
            //         'success' => false,
            //         'message' => 'PAN verification failed. Please check your PAN number.',
            //         'error' => $panVerification['message'] ?? 'Verification failed',
            //     ], 422);
            // }

            $panName = $panVerification['name'] ?? null;

            // Create or update personal details
            PersonalDetail::updateOrCreate(
                ['registration_id' => $userId],
                [
                    'father_name' => $request->father_name,
                    'mother_name' => $request->mother_name,
                    'dob' => $request->dob,
                    'gender' => $request->gender,
                    'occupation' => $request->occupation,
                    'marital_status' => $request->marital_status,
                    'pan_no' => strtoupper($request->pan_no),
                    'pan_name' => $panName,
                    'aadhaar_number' => $request->aadhaar_number,
                    'residential_status' => $request->residential_status,
                    'annual_income' => $request->annual_income,
                    'status' => 1,
                ]
            );

            // Update registration step
            if ($registration->step_number < 2) {
                $registration->update(['step_number' => 2]);
            }

            return response()->json([
                'success' => true,
                'message' => 'Personal information saved successfully',
                'pan_name' => $panName,
            ]);

        } catch (\Exception $e) {
            Log::error('Personal info submission failed', [
                'user_id' => $userId,
                'error' => $e->getMessage(),
            ]);

            return response()->json([
                'success' => false,
                'message' => 'An error occurred. Please try again.',
            ], 500);
        }
    }

    /**
     * Step 2: Submit Address.
     */
    public function submitAddress(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'permanent_address' => 'nullable|string',
            'permanent_address1' => 'nullable|string|max:255',
            'permanent_address2' => 'nullable|string|max:255',
            'permanent_address_city' => 'required|string|max:100',
            'permanent_address_country' => 'required|string|max:100',
            'permanent_address_pincode' => 'required|digits:6',
            'is_same' => 'nullable|boolean',
            'correspondence_address' => 'required_if:is_same,0|nullable|string',
            'correspondence_address1' => 'nullable|string|max:255',
            'correspondence_address2' => 'nullable|string|max:255',
            'correspondence_address_city' => 'required_if:is_same,0|nullable|string|max:100',
            'correspondence_address_country' => 'required_if:is_same,0|nullable|string|max:100',
            'correspondence_address_pincode' => 'required_if:is_same,0|nullable|digits:6',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'errors' => $validator->errors(),
            ], 422);
        }

        $userId = session('user_id');
        $registration = Registration::findOrFail($userId);

        try {
            $addressData = $request->only([
                'permanent_address',
                'permanent_address1',
                'permanent_address2',
                'permanent_address_city',
                'permanent_address_country',
                'permanent_address_pincode',
                'is_same',
            ]);

            // If same address, copy permanent to correspondence
            if ($request->is_same) {
                $addressData['correspondence_address'] = $request->permanent_address;
                $addressData['correspondence_address1'] = $request->permanent_address1;
                $addressData['correspondence_address2'] = $request->permanent_address2;
                $addressData['correspondence_address_city'] = $request->permanent_address_city;
                $addressData['correspondence_address_country'] = $request->permanent_address_country;
                $addressData['correspondence_address_pincode'] = $request->permanent_address_pincode;
            } else {
                $addressData = array_merge($addressData, $request->only([
                    'correspondence_address',
                    'correspondence_address1',
                    'correspondence_address2',
                    'correspondence_address_city',
                    'correspondence_address_country',
                    'correspondence_address_pincode',
                ]));
            }

            $addressData['status'] = 1;

            Address::updateOrCreate(
                ['registration_id' => $userId],
                $addressData
            );

            // Update registration step
            if ($registration->step_number < 3) {
                $registration->update(['step_number' => 3]);
            }

            return response()->json([
                'success' => true,
                'message' => 'Address saved successfully',
            ]);

        } catch (\Exception $e) {
            Log::error('Address submission failed', [
                'user_id' => $userId,
                'error' => $e->getMessage(),
            ]);

            return response()->json([
                'success' => false,
                'message' => 'An error occurred. Please try again.',
            ], 500);
        }
    }

    /**
     * Verify IFSC code.
     */
    public function verifyIfsc(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'ifsc_code' => 'required|string|size:11',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'errors' => $validator->errors(),
            ], 422);
        }

        $result = $this->bankService->getBankDetailsByIfsc($request->ifsc_code);

        return response()->json($result);
    }

    /**
     * Step 3: Submit Bank Details.
     */
    public function submitBankDetails(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'ifsc_code' => 'required|string|size:11',
            'account_number' => 'required|string|max:30',
            //'account_number_confirm' => 'required|same:account_number',
            //'account_type' => 'required|in:Savings,Current,Other',
            //'name_at_bank' => 'required|string|max:255',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'errors' => $validator->errors(),
            ], 422);
        }

        $userId = session('user_id');
        $registration = Registration::findOrFail($userId);

        try {
            // Get bank details from IFSC
            $bankDetails = $this->bankService->getBankDetailsByIfsc($request->ifsc_code);

            if (!$bankDetails['success']) {
                return response()->json([
                    'success' => false,
                    'message' => 'Invalid IFSC code',
                ], 422);
            }

            // Verify bank account with Sandbox API
            $verification = $this->sandboxApi->verifyBankAccount(
                $request->account_number,
                $request->ifsc_code
            );

            // Log verification attempt
            $this->sandboxApi->logBankVerification(
                $userId,
                $request->ip(),
                [
                    'account_number' => $request->account_number,
                    'ifsc' => $request->ifsc_code,
                    'verification_result' => $verification,
                ]
            );

            // Create or update bank details
            BankDetail::updateOrCreate(
                ['registration_id' => $userId],
                [
                    'ifsc_code' => strtoupper($request->ifsc_code),
                    'account_number' => $request->account_number,
                    'account_type' => $request->account_type,
                    'bank' => $bankDetails['bank'],
                    'branch' => $bankDetails['branch'],
                    'address' => $this->bankService->formatBankAddress($bankDetails),
                    'micr' => $bankDetails['micr'] ?? null,
                    'name_at_bank' => $request->name_at_bank,
                    'status' => 1,
                ]
            );

            // Update registration step
            if ($registration->step_number < 4) {
                $registration->update(['step_number' => 4]);
            }

            return response()->json([
                'success' => true,
                'message' => 'Bank details saved successfully',
                'verification' => $verification,
            ]);

        } catch (\Exception $e) {
            Log::error('Bank details submission failed', [
                'user_id' => $userId,
                'error' => $e->getMessage(),
            ]);

            return response()->json([
                'success' => false,
                'message' => 'An error occurred. Please try again.',
            ], 500);
        }
    }

    /**
     * Step 4: Submit Market Segments.
     */
    public function submitMarketSegments(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'cash' => 'nullable|boolean',
            'futures_options' => 'nullable|boolean',
            'commodity' => 'nullable|boolean',
            'currency' => 'nullable|boolean',
            'mutual_fund' => 'nullable|boolean',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'errors' => $validator->errors(),
            ], 422);
        }

        $userId = session('user_id');
        $registration = Registration::findOrFail($userId);

        // Check if at least one segment is selected
        $hasSelection = $request->cash || $request->futures_options ||
                       $request->commodity || $request->currency || $request->mutual_fund;

        if (!$hasSelection) {
            return response()->json([
                'success' => false,
                'message' => 'Please select at least one market segment',
            ], 422);
        }

        try {
            MarketSegment::updateOrCreate(
                ['registration_id' => $userId],
                [
                    'cash' => $request->cash ? 1 : 0,
                    'futures_options' => $request->futures_options ? 1 : 0,
                    'commodity' => $request->commodity ? 1 : 0,
                    'currency' => $request->currency ? 1 : 0,
                    'mutual_fund' => $request->mutual_fund ? 1 : 0,
                    'status' => 1,
                ]
            );

            // Update registration step
            if ($registration->step_number < 5) {
                $registration->update(['step_number' => 5]);
            }

            return response()->json([
                'success' => true,
                'message' => 'Market segments saved successfully',
            ]);

        } catch (\Exception $e) {
            Log::error('Market segments submission failed', [
                'user_id' => $userId,
                'error' => $e->getMessage(),
            ]);

            return response()->json([
                'success' => false,
                'message' => 'An error occurred. Please try again.',
            ], 500);
        }
    }

    // step 5
    public function submitRegulatoryInfo(Request $request)
{
    $validator = Validator::make($request->all(), [
        'number_of_years_of_investment' => 'required|string|max:50',
        'pep' => 'required|string|in:Yes,No',
        'name_of_pep' => 'nullable|string|max:255',
        'relation_with_pep' => 'nullable|string|max:255',
        'any_action_by_sebi' => 'required|string|in:Yes,No',
        'details_of_action' => 'nullable|string|max:500',
        'dealing_with_other_stockbroker' => 'required|string|in:Yes,No',
        'any_dispute_with_stockbroker' => 'required|string|in:Yes,No',
        'dispute_with_stockbroker_details' => 'nullable|string|max:500',
        'commodity_trade_classification' => 'required|string|max:100',
    ]);

    if ($validator->fails()) {
        return response()->json([
            'success' => false,
            'errors' => $validator->errors(),
        ], 422);
    }

    $userId = session('user_id');
    $registration = Registration::findOrFail($userId);

    try {
        RegulatoryInfo::updateOrCreate(
            ['registration_id' => $userId],
            [
                'number_of_years_of_investment' => $request->number_of_years_of_investment,
                'pep' => $request->pep,
                'name_of_pep' => $request->pep === 'Yes' ? $request->name_of_pep : null,
                'relation_with_pep' => $request->pep === 'Yes' ? $request->relation_with_pep : null,
                'any_action_by_sebi' => $request->any_action_by_sebi,
                'details_of_action' => $request->any_action_by_sebi === 'Yes' ? $request->details_of_action : null,
                'dealing_with_other_stockbroker' => $request->dealing_with_other_stockbroker,
                'any_dispute_with_stockbroker' => $request->any_dispute_with_stockbroker,
                'dispute_with_stockbroker_details' => $request->any_dispute_with_stockbroker === 'Yes' ? $request->dispute_with_stockbroker_details : null,
                'commodity_trade_classification' => $request->commodity_trade_classification,
                'status' => 1,
            ]
        );

        // Update registration step
        if ($registration->step_number < 6) {
            $registration->update(['step_number' => 6]);
        }

        return response()->json([
            'success' => true,
            'message' => 'Regulatory information saved successfully',
        ]);

    } catch (\Exception $e) {
        Log::error('Regulatory info submission failed', [
            'user_id' => $userId,
            'error' => $e->getMessage(),
        ]);

        return response()->json([
            'success' => false,
            'message' => 'An error occurred. Please try again later.',
        ], 500);
    }
}


// step 6 
public function submitDisclosures(Request $request)
{
    // $validator = Validator::make($request->all(), [
    //     'doc1' => 'required|accepted',
    //     'doc2' => 'required|accepted',
    //     'doc3' => 'required|accepted',
    //     'doc4' => 'required|accepted',
    // ], [
    //     'doc1.required' => 'Please confirm Rights and Obligations document.',
    //     'doc2.required' => 'Please confirm Risk Disclosure Document.',
    //     'doc3.required' => 'Please confirm Policy and Procedure.',
    //     'doc4.required' => 'Please confirm Tariff Sheet.',
    // ]);

    // if ($validator->fails()) {
    //     return response()->json([
    //         'success' => false,
    //         'errors' => $validator->errors(),
    //     ], 422);
    // }

    $userId = session('user_id');
    $registration = Registration::findOrFail($userId);

    try {
        // Disclosure::updateOrCreate(
        //     ['registration_id' => $userId],
        //     [
        //         'rights_obligations' => 1,
        //         'risk_disclosure' => 1,
        //         'policy_procedure' => 1,
        //         'tariff_sheet' => 1,
        //         'status' => 1,
        //     ]
        // );

        // Update registration step
        if ($registration->step_number < 7) {
            $registration->update(['step_number' => 7]);
        }

        return response()->json([
            'success' => true,
            'message' => 'Disclosures confirmed successfully',
        ]);

    } catch (\Exception $e) {
        Log::error('Disclosure submission failed', [
            'user_id' => $userId,
            'error' => $e->getMessage(),
        ]);

        return response()->json([
            'success' => false,
            'message' => 'An error occurred. Please try again.',
        ], 500);
    }
}


    /**
     * Get current KYC progress.
     */
    public function getProgress()
    {
        $userId = session('user_id');
        $registration = Registration::findOrFail($userId);

        return response()->json([
            'success' => true,
            'current_step' => $registration->step_number,
            'total_steps' => 6,
            'is_complete' => $registration->isComplete(),
        ]);
    }
}
