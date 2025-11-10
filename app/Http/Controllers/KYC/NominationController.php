<?php

namespace App\Http\Controllers\KYC;

use App\Http\Controllers\Controller;
use App\Models\Registration;
use App\Models\Nomination;
use App\Models\NominationDetail;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Log;

class NominationController extends Controller
{
    /**
     * Show nomination form.
     */
    public function showForm(Request $request)
    {
        $userId = session('user_id');
        $registration = Registration::with(['nomination.nominationDetails'])->findOrFail($userId);

        // Get nomination data if exists
        $nomination = $registration->nomination;
        $nominationDetails = $nomination ? $nomination->nominationDetails : collect([]);

        // Get all countries for dropdown
        $countries = \App\Models\Country::where('status', 1)->orderBy('country')->get();

        return view('kyc.nomination', compact('registration', 'nomination', 'nominationDetails', 'countries'));
    }

    /**
     * Submit nomination form.
     */
    public function submitNomination(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'nomination_form_id' => 'nullable|integer',
            'nominee_minor' => 'required|in:0,1',
            'relation_applicant_name' => 'nullable|string|max:255',
            'date_of_birth' => 'nullable|date',

            // Guardian details (required if nominee is minor)
            'guardian_name' => 'required_if:nominee_minor,1|nullable|string|max:255',
            'guardian_city' => 'required_if:nominee_minor,1|nullable|string|max:100',
            'guardian_address' => 'required_if:nominee_minor,1|nullable|string',
            'guardian_state' => 'required_if:nominee_minor,1|nullable|string|max:100',
            'guardian_country' => 'required_if:nominee_minor,1|nullable|string|max:100',
            'guardian_pin_code' => 'required_if:nominee_minor,1|nullable|string|size:6|regex:/^[0-9]{6}$/',
            'guardian_mobile' => 'required_if:nominee_minor,1|nullable|string|size:10|regex:/^[0-9]{10}$/',
            'guardian_email' => 'required_if:nominee_minor,1|nullable|email',
            'relation_of_guardian' => 'required_if:nominee_minor,1|nullable|string|max:100',
            'guardian_identification' => 'required_if:nominee_minor,1|nullable|string|max:100',
            'guardian_document' => 'nullable|file|mimes:jpg,jpeg,png,pdf|max:5120',

            // Nominee details arrays
            'name_of_nominee' => 'required|array|min:1',
            'name_of_nominee.*' => 'required|string|max:255',
            'nominee_mobile' => 'required|array|min:1',
            'nominee_mobile.*' => 'required|string|size:10|regex:/^[0-9]{10}$/',
            'nominee_email' => 'required|array|min:1',
            'nominee_email.*' => 'required|email',
            'share_of_nominees' => 'required|array|min:1',
            'share_of_nominees.*' => 'required|numeric|min:0|max:100',
            'relation_applicant_name_nominees' => 'nullable|array',
            'relation_applicant_name_nominees.*' => 'nullable|string|max:255',
            'nominee_address' => 'required|array|min:1',
            'nominee_address.*' => 'required|string',
            'nominee_city' => 'required|array|min:1',
            'nominee_city.*' => 'required|string|max:100',
            'nominee_state' => 'required|array|min:1',
            'nominee_state.*' => 'required|string|max:100',
            'nominee_country' => 'required|array|min:1',
            'nominee_country.*' => 'required|integer',
            'nominee_pin_code' => 'required|array|min:1',
            'nominee_pin_code.*' => 'required|string|size:6|regex:/^[0-9]{6}$/',
            'nominee_dob' => 'required|array|min:1',
            'nominee_dob.*' => 'required|date',
        ], [
            'guardian_pin_code.size' => 'Guardian pin code must be 6 digits',
            'guardian_pin_code.regex' => 'Guardian pin code must contain only numbers',
            'guardian_mobile.size' => 'Guardian mobile must be 10 digits',
            'guardian_mobile.regex' => 'Guardian mobile must contain only numbers',
            'nominee_mobile.*.size' => 'Nominee mobile must be 10 digits',
            'nominee_mobile.*.regex' => 'Nominee mobile must contain only numbers',
            'nominee_pin_code.*.size' => 'Nominee pin code must be 6 digits',
            'nominee_pin_code.*.regex' => 'Nominee pin code must contain only numbers',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'errors' => $validator->errors(),
            ], 422);
        }
      //  dd($request->all());

        // Validate total share is 100%
        $totalShare = array_sum($request->share_of_nominees);
        if ($totalShare != 100) {
            return response()->json([
                'success' => false,
                'message' => 'Total share of nominees must be 100%',
            ], 422);
        }

        $userId = session('user_id');
        $registration = Registration::findOrFail($userId);

        try {
            // Handle guardian document upload if exists
            $guardianDocumentPath = null;
            if ($request->hasFile('guardian_document')) {
                $file = $request->file('guardian_document');
                $fileName = time() . '_guardian_' . $file->getClientOriginalName();
                $guardianDocumentPath = $file->storeAs('uploads/nomination/guardian', $fileName, 'public');
            }

            // Create or update nomination
            $nominationData = [
                'registration_id' => $userId,
                'nominee_minor' => $request->nominee_minor,
                'relation_applicant_name' => $request->relation_applicant_name,
                'date_of_birth' => $request->date_of_birth,
                'status' => 1,
            ];

            // Add guardian details if nominee is minor
            if ($request->nominee_minor == 1 || $request->nominee_minor === '1') {
                $nominationData['guardian_name'] = $request->guardian_name;
                $nominationData['guardian_city'] = $request->guardian_city;
                $nominationData['guardian_address'] = $request->guardian_address;
                $nominationData['guardian_state'] = $request->guardian_state;
                $nominationData['guardian_country'] = $request->guardian_country;
                $nominationData['guardian_pin_code'] = $request->guardian_pin_code;
                $nominationData['guardian_mobile'] = $request->guardian_mobile;
                $nominationData['guardian_email'] = $request->guardian_email;
                $nominationData['relation_of_guardian'] = $request->relation_of_guardian;
                $nominationData['guardian_identification'] = $request->guardian_identification;
                if ($guardianDocumentPath) {
                    $nominationData['guardian_document'] = $guardianDocumentPath;
                }
            }

            $nomination = Nomination::updateOrCreate(
                ['registration_id' => $userId],
                $nominationData
            );

            // Delete existing nomination details and create new ones
            NominationDetail::where('nomination_id', $nomination->id)->delete();
            
            // Create nomination details for each nominee
            foreach ($request->name_of_nominee as $index => $name) {
                // Handle nominee document upload
                $nomineeDocumentPath = null;
                $documentFieldName = 'nominee_document_' . $index;

                if ($request->hasFile($documentFieldName)) {
                    $file = $request->file($documentFieldName);
                    $fileName = time() . '_nominee_' . $index . '_' . $file->getClientOriginalName();
                    $nomineeDocumentPath = $file->storeAs('uploads/nomination/nominee', $fileName, 'public');
                }

                // Get nominee identification type
                $identificationFieldName = 'nominee_identification_' . $index;
                $nomineeIdentification = $request->input($identificationFieldName);

                NominationDetail::create([
                    'nomination_id' => $nomination->id,
                    'name_of_nominee' => $name,
                    'nominee_mobile' => $request->nominee_mobile[$index],
                    'nominee_email' => $request->nominee_email[$index],
                    'share_of_nominees' => $request->share_of_nominees[$index],
                    'relation_applicant_name_nominees' => $request->relation_applicant_name_nominees[$index] ?? null,
                    'nominee_address' => $request->nominee_address[$index],
                    'nominee_city' => $request->nominee_city[$index],
                    'nominee_state' => $request->nominee_state[$index],
                    'nominees_country' => $request->nominee_country[$index], // Note: column name is plural
                    'nominee_pin_code' => $request->nominee_pin_code[$index],
                    'nominee_identification' => $nomineeIdentification,
                    'nominee_document' => $nomineeDocumentPath,
                    'status' => 1,
                ]);
            }
            // Update registration step
            if ($registration->step_number < 8) {
                $registration->update(['step_number' => 8]);
            }

            return response()->json([
                'success' => true,
                'message' => 'Nomination details saved successfully',
                'redirect' => route('kyc.documents'),
            ]);

        } catch (\Exception $e) {
            Log::error('Nomination submission failed', [
                'user_id' => $userId,
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
                'line' => $e->getLine(),
                'file' => $e->getFile(),
            ]);

            // In development, show detailed error
            if (config('app.debug')) {
                return response()->json([
                    'success' => false,
                    'message' => 'Error: ' . $e->getMessage(),
                    'file' => $e->getFile(),
                    'line' => $e->getLine(),
                ], 500);
            }

            return response()->json([
                'success' => false,
                'message' => 'An error occurred. Please try again.',
            ], 500);
        }
    }

    /**
     * Delete a nominee.
     */
    public function deleteNominee(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'nominee_id' => 'required|integer|exists:nomination_details,id',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'errors' => $validator->errors(),
            ], 422);
        }

        try {
            $userId = session('user_id');
            $nomineeDetail = NominationDetail::find($request->nominee_id);

            // Verify that the nominee belongs to the current user
            $nomination = Nomination::where('id', $nomineeDetail->nomination_id)
                ->where('registration_id', $userId)
                ->first();

            if (!$nomination) {
                return response()->json([
                    'success' => false,
                    'message' => 'Unauthorized access',
                ], 403);
            }

            $nomineeDetail->delete();

            return response()->json([
                'success' => true,
                'message' => 'Nominee deleted successfully',
            ]);

        } catch (\Exception $e) {
            Log::error('Delete nominee failed', [
                'user_id' => $userId,
                'nominee_id' => $request->nominee_id,
                'error' => $e->getMessage(),
            ]);

            return response()->json([
                'success' => false,
                'message' => 'An error occurred. Please try again.',
            ], 500);
        }
    }
}
