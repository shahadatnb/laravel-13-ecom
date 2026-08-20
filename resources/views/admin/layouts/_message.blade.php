{{--
    Flash messages are now also converted to toasts via JavaScript (convertFlashToasts).
    The data-flash-toast attributes allow the JS to pick them up and show as toasts.
    The visible alerts remain as a fallback for non-JS environments.
--}}
{{--
    Flash messages are displayed both as visible alerts (fallback for non-JS)
    AND as toasts via JavaScript (convertFlashToasts picks up data-flash-toast attr).
    The JS will auto-dismiss the visible alert after showing the toast.
--}}
@if (session('success'))
    <div class="alert alert-success alert-dismissible" data-flash-toast="success">
        <button type="button" class="close" data-dismiss="alert" aria-hidden="true">×</button>
        <h5><i class="icon fas fa-check"></i> Success!</h5>
        {{ session('success') }}
    </div>
@endif

@if (session('error'))
    <div class="alert alert-danger alert-dismissible" data-flash-toast="error">
        <button type="button" class="close" data-dismiss="alert" aria-hidden="true">×</button>
        <h5><i class="icon fas fa-times"></i> Error!</h5>
        {{ session('error') }}
    </div>
@endif

@if (session('warning'))
    <div class="alert alert-warning alert-dismissible" data-flash-toast="warning">
        <button type="button" class="close" data-dismiss="alert" aria-hidden="true">×</button>
        <h5><i class="icon fas fa-exclamation-triangle"></i> Warning!</h5>
        {{ session('warning') }}
    </div>
@endif

@if (session('info'))
    <div class="alert alert-info alert-dismissible" data-flash-toast="info">
        <button type="button" class="close" data-dismiss="alert" aria-hidden="true">×</button>
        <h5><i class="icon fas fa-info"></i> Info!</h5>
        {{ session('info') }}
    </div>
@endif
