@extends('temp.common')

@section('title', 'Customer Profile')
@section('head')
    <link href="https://cdn.jsdelivr.net/npm/quill@2.0.3/dist/quill.snow.css" rel="stylesheet" />
    <style>
        .crm-badge-missing {
            display: inline-block;
            padding: 2px 8px;
            font-size: 11px;
            font-weight: 600;
            color: #dc3545;
            background: rgba(220, 53, 69, 0.08);
            border-radius: 20px;
        }

        .crm-activity-timeline {
            position: relative;
            padding-left: 32px;
        }

        .crm-activity-timeline::before {
            content: '';
            position: absolute;
            left: 14px;
            top: 0;
            bottom: 0;
            width: 2px;
            background: rgba(0, 0, 0, 0.08);
        }

        .crm-activity-item {
            position: relative;
            display: flex;
            gap: 16px;
            margin-bottom: 24px;
        }

        .crm-activity-icon {
            width: 28px;
            height: 28px;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            color: #fff;
            font-size: 12px;
            flex-shrink: 0;
            z-index: 0;
        }

        .crm-activity-content {
            background: #fff;
            border-radius: 10px;
            padding: 12px 14px;
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.04);
            width: 100%;
        }

        .crm-activity-message {
            margin-top: 6px;
            font-size: 14px;
            line-height: 1.5;
        }

        .crm-doc-grid {
            --card-radius: 12px;
        }

        .crm-doc-card {
            background: #fff;
            border-radius: var(--card-radius);
            box-shadow: 0 4px 14px rgba(0, 0, 0, 0.05);
            overflow: hidden;
            display: flex;
            flex-direction: column;
            height: 100%;
        }

        .crm-doc-preview {
            height: 120px;
            background: #f4f5fa;
            display: flex;
            align-items: center;
            justify-content: center;
        }

        .crm-doc-preview img {
            max-height: 100%;
            max-width: 100%;
            object-fit: contain;
        }

        .crm-doc-preview i {
            font-size: 32px;
            color: #dc3545;
        }

        .crm-doc-body {
            padding: 10px 12px;
            flex-grow: 1;
        }

        .crm-doc-actions {
            padding: 8px 10px;
            border-top: 1px solid rgba(0, 0, 0, 0.05);
            display: flex;
            justify-content: space-between;
        }
    </style>
@endsection
@section('content')

    <div class="container-fluid">
        <div class="crm-page">

            {{-- HEADER --}}
            <div class="d-flex justify-content-between align-items-center mb-2">
                <div>
                    <h4 class="mb-1">
                        <i class="fa-solid fa-user me-2"></i>
                        {{ $customer->name }}
                    </h4>
                    <div class="text-muted small">
                        {{ $customer->mobile }}
                        @if ($customer->email)
                            · {{ $customer->email }}
                        @endif
                    </div>
                </div>
                @can('project.customer.edit')
                    <div class="d-flex gap-2">
                        <a href="{{ route('customers.view.edit', $customer->id) }}" class="btn btn-sm btn-outline-primary">
                            <i class="fa-solid fa-pen me-1"></i>
                            Edit
                        </a>
                    </div>
                @endcan
            </div>

            <div class="crm-section mt-3">
                <h6 class="mb-3">
                    <i class="fa-solid fa-user me-1"></i>
                    Basic Details
                </h6>
                <div class="row g-3">
                    <div class="col-md-3 d-flex align-items-end">
                        @include('page.customers.partials.doc_uploader', [
                            'type' => 'passport_size_photo',
                            'label' => 'Passport Size Photo',
                            'placeholder' => asset('assets/img/placeholder/user.jpg'),
                            'view' => true,
                        ])
                    </div>
                    <div class="col-md-9">
                        <div class="row gy-4">
                            <div class="col-md-4">
                                <div class="text-muted small">Full Name</div>
                                <div class="fw-semibold">{{ $customer->name ?? '—' }}</div>
                            </div>

                            <div class="col-md-4">
                                <div class="text-muted small">Mobile</div>
                                <div class="fw-semibold">{{ $customer->mobile ?? '—' }}</div>
                            </div>

                            <div class="col-md-4">
                                <div class="text-muted small">Alternate Mobile</div>
                                <div class="fw-semibold">{{ $customer->alternate_mobile ?? '—' }}</div>
                            </div>

                            <div class="col-md-6">
                                <div class="text-muted small">Email</div>
                                <div class="fw-semibold">{{ $customer->email ?? '—' }}</div>
                            </div>

                            <div class="col-md-6">
                                <div class="text-muted small">Address</div>
                                <div class="fw-semibold">{{ $customer->address ?? '—' }}</div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="crm-section mt-3">
                <h6 class="mb-3">
                    <i class="fa-solid fa-id-card me-1"></i>
                    Identity & KYC
                </h6>

                <div class="row g-3">

                    {{-- AADHAR --}}
                    <div class="col-md-3 d-flex align-items-end">
                        @include('page.customers.partials.doc_uploader', [
                            'type' => 'aadhar_card',
                            'label' => 'Aadhar Card',
                            'view' => true,
                            'placeholder' => asset('assets/img/placeholder/aadhar-card.png'),
                        ])
                    </div>
                    <div class="col-md-3">
                        <div class="text-muted small">Aadhar Number</div>
                        <div class="fw-semibold">{{ $customer->aadhar_card_number ?? '—' }}</div>
                    </div>

                    {{-- PAN --}}
                    <div class="col-md-3 d-flex align-items-end">
                        @include('page.customers.partials.doc_uploader', [
                            'type' => 'pan_card',
                            'label' => 'PAN Card',
                            'view' => true,
                            'placeholder' => asset('assets/img/placeholder/pan-card.png'),
                        ])
                    </div>
                    <div class="col-md-3">
                        <div class="text-muted small">PAN Number</div>
                        <div class="fw-semibold">{{ $customer->pan_card_number ?? '—' }}</div>
                    </div>

                </div>
            </div>
            <div class="crm-section mt-3">
                <h6 class="mb-3">
                    <i class="fa-solid fa-building-columns me-1"></i>
                    Bank Details
                </h6>

                <div class="row g-3">
                    <div class="col-md-5">
                        @include('page.customers.partials.doc_uploader', [
                            'type' => 'cancel_cheque',
                            'label' => 'Cancelled Cheque',
                            'view' => true,
                            'placeholder' => asset('assets/img/placeholder/chaque-2.png'),
                        ])
                    </div>
                    <div class="col-md-7">
                        <div class="row gy-4">
                            <div class="col-md-4">
                                <div class="text-muted small">Bank Name</div>
                                <div class="fw-semibold">{{ $customer->bank_name ?? '—' }}</div>
                            </div>
        
                            <div class="col-md-4">
                                <div class="text-muted small">IFSC Code</div>
                                <div class="fw-semibold">{{ $customer->ifsc_code ?? '—' }}</div>
                            </div>
        
                            <div class="col-md-4">
                                <div class="text-muted small">Account Holder Name</div>
                                <div class="fw-semibold">{{ $customer->ac_holder_name ?? '—' }}</div>
                            </div>
        
                            <div class="col-md-4">
                                <div class="text-muted small">Account Number</div>
                                <div class="fw-semibold">{{ $customer->bank_account_number ?? '—' }}</div>
                            </div>
        
                            <div class="col-md-4">
                                <div class="text-muted small">Branch Name</div>
                                <div class="fw-semibold">{{ $customer->branch_name ?? '—' }}</div>
                            </div>
                        </div>
                    </div>

                </div>
            </div>
            <div class="crm-section mt-3">
                <h6 class="mb-3">
                    <i class="fa-solid fa-tower-observation me-1"></i>
                    Electricity Details
                </h6>

                <div class="row g-3">
                    <div class="col-md-5">
                        @include('page.customers.partials.doc_uploader', [
                            'type' => 'light_bill',
                            'label' => 'Light Bill',
                            'view' => true,
                            'placeholder' => asset('assets/img/placeholder/light-bill.png')
                        ])
                    </div>
                    <div class="col-md-7">
                        <div class="d-flex flex-column gap-4">
                            <div class="">
                                <div class="text-muted small">Light Bill Number</div>
                                <div class="fw-semibold">{{ $customer->lightbill_number ?? '—' }}</div>
                            </div>
        
                            <div class="">
                                <div class="text-muted small">Sanction Load</div>
                                <div class="fw-semibold">{{ $customer->sanction_load ?? '—' }}</div>
                            </div>
        
                            <div class="">
                                <div class="text-muted small">Service Number</div>
                                <div class="fw-semibold">{{ $customer->service_number ?? '—' }}</div>
                            </div>
                        </div>
                    </div>

                </div>
            </div>


            {{-- NOTES --}}
            <div class="crm-section mt-3">
                <div id="customerNotes">
                    <div class="mb-3">
                        <div id="customerNote">{!! $customer->note !!}</div>
                    </div>
                </div>
            </div>

            {{-- ACTIVITIES --}}
            <div class="crm-section mt-3">
                <h6 class="mb-2">Activity Timeline</h6>

                <div id="customerActivities">
                    <div class="text-muted small">Loading activities…</div>
                </div>
            </div>


            {{-- DOCUMENTS --}}
            <div class="crm-section mt-3">
                <div id="customerDocuments">
                    <div class="text-muted small">Loading documents…</div>
                </div>
            </div>

        </div>
    </div>
@endsection

@push('scripts')
    <script>
        const CUSTOMER_ID = {{ $customer->id }};
        window.CSRF_TOKEN = "{{ csrf_token() }}"
        const ACTIVITY_URL = "{{ route('customers.ajax.activities', $customer->id) }}";
        const DOCUMENTS_URL = "{{ route('customers.ajax.documents', $customer->id) }}";
    </script>
    <script src="https://cdn.jsdelivr.net/npm/quill@2.0.3/dist/quill.js"></script>
    <script src="{{ asset('assets/js/page/customers/view.js') }}"></script>
@endpush
