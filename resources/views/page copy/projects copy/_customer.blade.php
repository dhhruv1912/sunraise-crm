<div class="row">

    {{-- LEFT: Customer Basic Fields --}}
    <div class="col-9">

        {{-- Customer Name --}}
        <div class="row mb-3">
            <label class="col-sm-2 col-form-label" for="customer_name">Name</label>
            <div class="col-sm-10">
                <div class="input-group input-group-merge">
                    <span class="input-group-text"><i class="mdi mdi-account-outline"></i></span>
                    <input type="text"
                           class="form-control"
                           id="customer_name"
                           name="customer_name"
                           value="{{ $project->customer_name }}"
                           placeholder="John Doe">
                </div>
            </div>
        </div>

        {{-- Email --}}
        <div class="row mb-3">
            <label class="col-sm-2 col-form-label" for="email">Email</label>
            <div class="col-sm-10">
                <div class="input-group input-group-merge">
                    <span class="input-group-text"><i class="mdi mdi-email-outline"></i></span>
                    <input type="email"
                           class="form-control"
                           id="email"
                           name="email"
                           value="{{ $project->email }}"
                           placeholder="john@example.com"
                           autocomplete="off">
                </div>
            </div>
        </div>

        {{-- Mobile --}}
        <div class="row mb-3">
            <label class="col-sm-2 col-form-label" for="mobile">Mobile</label>
            <div class="col-sm-10">
                <div class="input-group input-group-merge">
                    <span class="input-group-text"><i class="mdi mdi-phone"></i></span>
                    <input type="text"
                           class="form-control"
                           id="mobile"
                           name="mobile"
                           value="{{ $project->mobile }}"
                           placeholder="9876543210">
                </div>
            </div>
        </div>

        {{-- Address --}}
        <div class="row mb-3">
            <label class="col-sm-2 col-form-label" for="address">Address</label>
            <div class="col-sm-10">
                <div class="input-group input-group-merge">
                    <span class="input-group-text"><i class="mdi mdi-home-outline"></i></span>
                    <textarea class="form-control"
                              id="address"
                              name="address"
                              placeholder="Customer full address"
                              rows="2">{{ $project->address }}</textarea>
                </div>
            </div>
        </div>

    </div>


    {{-- RIGHT: Passport Size Photo --}}
    <div class="col-3 border border-dark p-1 rounded position-relative document-upload-element"
         data-placeholder_url="{{ asset('assets/admin/img/placeholder/user.jpg') }}">

        {{-- Remove Button --}}
        <button type="button"
                class="remove-document-icon btn btn-danger btn-icon h-100 position-absolute w-100 waves-effect waves-light d-none"
                style="bottom:0;left:0;opacity:0.5;">
            <span class="tf-icons mdi mdi-close"></span> Remove
        </button>

        {{-- Upload Button --}}
        <button type="button"
                class="upload-document-icon btn btn-primary btn-icon h-100 position-absolute w-100 waves-effect waves-light d-none"
                style="bottom:0;left:0;opacity:0.5;">
            <span class="tf-icons mdi mdi-plus"></span> Add
        </button>

        {{-- File Input --}}
        <input type="file" class="d-none" name="passport_size_photo" id="passport_size_photo" accept="image/*">
        <input type="hidden" name="passport_size_photo_hidden"
               id="passport_size_photo_hidden"
               value="{{ $project->passport_size_photo }}">

        {{-- Preview --}}
        <img src="@if($project->passport_size_photo)
                    {{ asset('storage/' . $project->passport_size_photo) }}
                  @else
                    {{ asset('assets/admin/img/placeholder/user.jpg') }}
                  @endif"
             class="rounded w-100 placeholder_img"
             style="object-fit:cover;">
    </div>

</div>
