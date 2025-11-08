@extends('layouts.app')

@section('title', 'Thank You - SKI Capital')

@section('content')
<style>
    .thank-you-container {
        background: #fff;
        padding: 50px 30px;
        border-radius: 8px;
        box-shadow: 0 0 15px rgba(0,0,0,0.1);
        text-align: center;
        margin: 50px 0;
    }
    .icon-check {
        font-size: 80px;
        color: #5b6b3d;
        margin-bottom: 20px;
    }
    .success-icon {
        width: 80px;
        height: 80px;
        background: #5b6b3d;
        border-radius: 50%;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        margin-bottom: 20px;
    }
    .success-icon i {
        font-size: 40px;
        color: white;
    }
    .thank-you-title {
        font-size: 28px;
        font-weight: 600;
        color: #333;
        margin-bottom: 20px;
    }
    .application-info {
        background: #f8f9fa;
        padding: 20px;
        border-radius: 5px;
        margin: 20px 0;
    }
    .application-info p {
        margin: 10px 0;
        font-size: 16px;
    }
    .ipv-notice {
        background: #fff3cd;
        border: 2px solid #ffc107;
        padding: 20px;
        border-radius: 5px;
        margin: 30px 0;
    }
    .ipv-notice strong {
        font-size: 18px;
        color: #856404;
    }
    .btn-ipv {
        background: #dc3545;
        color: white;
        padding: 10px 30px;
        text-decoration: none;
        display: inline-block;
        border-radius: 5px;
        margin-top: 10px;
        font-weight: 600;
    }
    .btn-ipv:hover {
        background: #c82333;
        color: white;
    }
    .contact-info {
        margin-top: 30px;
        padding: 20px;
        background: #e9ecef;
        border-radius: 5px;
    }
</style>

<div class="container">
    <div class="row">
        <div class="col-lg-8 mx-auto">
            <div class="thank-you-container">
                <div class="success-icon">
                    <i class="fas fa-check"></i>
                </div>

                <h2 class="thank-you-title">Congratulations!</h2>

                <p style="font-size: 16px; line-height: 1.6;">
                    You have successfully submitted your trading and demat account application and the same is under process.
                </p>

                <div class="application-info">
                    <p><strong>Application Number:</strong> {{ $registration->application_number }}</p>
                    <p><strong>Date:</strong> {{ $registration->application_date ? $registration->application_date->format('d-m-Y') : date('d-m-Y') }}</p>
                </div>

                @if($registration->kyc_uploaded == 0)
                <div class="ipv-notice">
                    <p><strong>Complete Your In Person Verification (IPV)</strong></p>
                    <p>Please complete your video verification to proceed with your application.</p>
                    <a href="{{ url('/ipv/permission') }}" class="btn-ipv">Start IPV Now</a>
                </div>

                <div class="contact-info">
                    <p style="margin-bottom: 15px;">If you haven't uploaded your KYC documents yet, you may:</p>
                    <ul style="list-style: none; padding: 0;">
                        <li style="margin: 10px 0;">
                            <a href="{{ route('kyc.documents') }}" style="color: #5b6b3d; font-weight: 600;">Upload documents here</a>
                        </li>
                        <li style="margin: 10px 0;">
                            Email documents to: <a href="mailto:ekyc@skicapital.net" style="color: #5b6b3d; font-weight: 600;">ekyc@skicapital.net</a>
                        </li>
                        <li style="margin: 10px 0;">
                            WhatsApp on: <a href="https://api.whatsapp.com/send?phone=919910785149&text=Hi,%20I%20need%20help%20regarding%20KYC%20documents" style="color: #5b6b3d; font-weight: 600;">+91 9910785149</a>
                        </li>
                    </ul>
                </div>
                @endif

                <div style="margin-top: 30px;">
                    <p>Our representative will contact you shortly with your account and login credentials.</p>
                    <p>Feel free to contact us should you have any questions or require any assistance regarding your account.</p>
                </div>

                <div style="margin-top: 30px;">
                    <a href="{{ route('home') }}" class="btn btn-primary">Go to Home</a>
                    @if($registration->kyc_uploaded == 0)
                    <a href="{{ route('kyc.documents') }}" class="btn btn-success">Upload Documents</a>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
