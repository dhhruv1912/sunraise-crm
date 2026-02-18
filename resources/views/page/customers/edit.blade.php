@extends('temp.common')

@section('title', 'Edit Customer')
@section('head')
    <style>
        .crm-doc-upload-box {
            border: 1px dashed rgba(0, 0, 0, 0.15);
            border-radius: 10px;
            padding: 16px;
            text-align: center;
            cursor: pointer;
            transition: all .2s ease;
        }

        .crm-doc-upload-box:hover {
            background: rgba(0, 0, 0, 0.03);
            border-color: var(--bs-primary);
        }

        .crm-icon-circle {
            width: 36px;
            height: 36px;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 14px;
        }

        .crm-sticky-actions {
            position: sticky;
            bottom: 55px;
            background: #fff;
            padding: 12px;
            border-top: 1px solid rgba(0, 0, 0, 0.08);
            display: flex;
            justify-content: flex-end;
            z-index: 5;
            border-radius: 10px;
            margin-top: 12px;
        }

        .crm-doc-preview-box {
            border: 1px dashed rgba(0, 0, 0, 0.15);
            border-radius: 10px;
            padding: 10px;
            text-align: center;
        }


        .crm-badge-missing {
            display: inline-block;
            font-weight: 600;
            color: #dc3545;
            background: rgba(220, 53, 69, 0.08);
            min-height: 250px;
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
                        <i class="fa-solid fa-user-pen me-2"></i>
                        Edit Customer
                    </h4>
                    <div class="text-muted small">
                        Update customer information & documents
                    </div>
                </div>

                <a href="{{ route('customers.view.show', $customer->id) }}" class="btn btn-sm btn-outline-secondary">
                    <i class="fa-solid fa-arrow-left me-1"></i>
                    Back
                </a>
            </div>

            <form id="customerForm">
                <div class="row g-3">

                    {{-- LEFT --}}
                    <div class="col-lg-8">

                        {{-- BASIC --}}
                        <div class="crm-section">
                            <div class="d-flex align-items-center gap-2 mb-3">
                                <div class="crm-icon-circle bg-primary-subtle text-primary">
                                    <i class="fa-solid fa-user"></i>
                                </div>
                                <h6 class="mb-0">Basic Details</h6>
                            </div>
                            <div class="row g-3">
                                @include('page.customers.partials.edit-basic', [
                                    'customer' => $customer,
                                ])
                            </div>
                        </div>

                        {{-- KYC --}}
                        <div class="crm-section mt-3">
                            <div class="d-flex align-items-center gap-2 mb-3">
                                <div class="crm-icon-circle bg-primary-subtle text-primary">
                                    <i class="fa-solid fa-address-card"></i>
                                </div>
                                <h6 class="mb-0">Identity & KYC</h6>
                            </div>

                            <div class="row g-3">

                                @include('page.customers.partials.edit-kyc', [
                                    'customer' => $customer,
                                ])
                            </div>
                        </div>

                        {{-- BANK --}}
                        <div class="crm-section mt-3">
                            <div class="d-flex align-items-center gap-2 mb-3">
                                <div class="crm-icon-circle bg-primary-subtle text-primary">
                                    <i class="fa-solid fa-building-columns"></i>
                                </div>
                                <h6 class="mb-0">Bank Details</h6>
                            </div>

                            <div class="row g-3">

                                @include('page.customers.partials.edit-bank', [
                                    'customer' => $customer,
                                ])
                            </div>
                        </div>

                    </div>

                    {{-- RIGHT --}}
                    <div class="col-lg-4">




                        {{-- ELECTRICITY --}}
                        <div class="crm-section">
                            <div class="d-flex align-items-center gap-2 mb-3">
                                <div class="crm-icon-circle bg-primary-subtle text-primary">
                                    <i class="fa-solid fa-tower-observation"></i>
                                </div>
                                <h6 class="mb-0">Electricity Details</h6>
                            </div>

                            <div class="row g-3">
                                @include('page.customers.partials.edit-electricity', [
                                    'customer' => $customer,
                                ])
                            </div>
                        </div>

                    </div>

                </div>

                {{-- BASIC DETAILS --}}


                {{-- IDENTITY + DOCUMENTS --}}


                {{-- ELECTRICITY --}}


                {{-- BANK DETAILS --}}


                {{-- ACTIONS --}}
                {{-- <div class="d-flex justify-content-end mt-3">
                    <button type="submit" class="btn btn-primary">
                        <i class="fa-solid fa-save me-1"></i>
                        Save Changes
                    </button>
                </div> --}}
                <div class="crm-sticky-actions">
                    <button type="submit" class="btn btn-primary">
                        <i class="fa-solid fa-save me-1"></i>
                        Save Changes
                    </button>
                </div>

            </form>
        </div>
    </div>
@endsection

@push('scripts')
    <script>
        const UPDATE_URL = "{{ route('customers.ajax.update', $customer->id) }}";
        const DOC_UPLOAD_URL = "{{ route('documents.ajax.uploadCustomer') }}";
        const ENTITY_ID = {{ $customer->id }};
        window.CSRF_TOKEN = "{{ csrf_token() }}"
    </script>
    <script src="{{ asset('assets/js/page/customers/form.js') }}"></script>
@endpush
