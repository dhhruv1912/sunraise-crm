{{--
    USAGE:
    showAlert("Message", "alert-primary");
    showDismissible("Saved successfully!", "alert-success");
    showConfirm("Are you sure?", "alert-warning", callbackTrue, callbackFalse);

    BOOTSTRAP CLASSES AVAILABLE:
    alert-primary | alert-secondary | alert-success | alert-danger |
    alert-warning | alert-info | alert-dark
--}}

{{-- SIMPLE ALERT --}}
<div id="alert-simple" class="alert d-none" role="alert"></div>

{{-- DISMISSIBLE ALERT --}}
<div id="alert-dismissible" class="alert d-none alert-dismissible fade show" role="alert">
    <span class="alert-text"></span>
    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
</div>

{{-- CONFIRM ALERT (OK / Cancel) --}}
<div id="alert-confirm" class="alert d-none d-flex align-items-center" role="alert">
    <span class="alert-text col"></span>
    <div class="ms-3">
        <button type="button" class="btn btn-outline-success rounded-pill me-2 btn-confirm-true">OK</button>
        <button type="button" class="btn btn-outline-danger rounded-pill btn-confirm-false">Cancel</button>
    </div>
</div>
