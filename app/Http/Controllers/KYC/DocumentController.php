<?php

namespace App\Http\Controllers\KYC;

use App\Http\Controllers\Controller;
use App\Models\Registration;
use App\Models\KycDocument;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Log;

class DocumentController extends Controller
{
    /**
     * Show document upload form.
     */
    public function showForm(Request $request)
    {
        $userId = session('user_id');
        $registration = Registration::with(['kycDocument'])->findOrFail($userId);

        // Get document data if exists
        $kycDocument = $registration->kycDocument;

        return view('kyc.documents', compact('registration', 'kycDocument'));
    }

    /**
     * Upload KYC documents.
     */
    public function uploadDocuments(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'pan_card' => 'required|file|mimes:jpg,jpeg,png,pdf|max:5120',
            'aadhaar_card' => 'required|file|mimes:jpg,jpeg,png,pdf|max:5120',
            'address_proof' => 'required|file|mimes:jpg,jpeg,png,pdf|max:5120',
            'bank_proof' => 'required|file|mimes:jpg,jpeg,png,pdf|max:5120',
            'signature' => 'required|file|mimes:jpg,jpeg,png,pdf|max:5120',
            'photo' => 'required|file|mimes:jpg,jpeg,png|max:5120',
            'income_proof' => 'nullable|file|mimes:jpg,jpeg,png,pdf|max:5120',
            'cancelled_cheque' => 'nullable|file|mimes:jpg,jpeg,png,pdf|max:5120',
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
            $documentPaths = [];

            // Upload each document
            $documents = [
                'pan_card',
                'aadhaar_card',
                'address_proof',
                'bank_proof',
                'signature',
                'photo',
                'income_proof',
                'cancelled_cheque'
            ];

            foreach ($documents as $docType) {
                if ($request->hasFile($docType)) {
                    $file = $request->file($docType);
                    $fileName = time() . '_' . $docType . '_' . $file->getClientOriginalName();
                    $path = $file->storeAs('uploads/kyc/' . $userId, $fileName, 'public');
                    $documentPaths[$docType] = $path;
                }
            }

            // Create or update KYC document record
            $kycDocument = KycDocument::updateOrCreate(
                ['registration_id' => $userId],
                array_merge($documentPaths, [
                    'status' => 1,
                    'uploaded_on' => now(),
                ])
            );

            // Update registration
            $registration->update([
                'step_number' => 9,
                'kyc_uploaded' => 1,
            ]);

            // Generate application number if not already generated
            if (!$registration->application_number) {
                $registration->generateApplicationNumber();
            }

            return response()->json([
                'success' => true,
                'message' => 'Documents uploaded successfully',
                'redirect' => route('dashboard'), // Or thank you page
            ]);

        } catch (\Exception $e) {
            Log::error('Document upload failed', [
                'user_id' => $userId,
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
            ]);

            // Clean up uploaded files if there was an error
            foreach ($documentPaths ?? [] as $path) {
                if (Storage::disk('public')->exists($path)) {
                    Storage::disk('public')->delete($path);
                }
            }

            return response()->json([
                'success' => false,
                'message' => 'An error occurred while uploading documents. Please try again.',
            ], 500);
        }
    }
}
