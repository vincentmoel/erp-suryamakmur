{{--
    Reusable delete confirmation dialog.

    Usage — trigger any .dt-delete-form submit via JS (see app.js),
    or open manually with: data-dialog-open="#confirm-delete-dialog"

    The dialog intercepts form.dt-delete-form submits globally.
    To use for non-datatable forms, add class="dt-delete-form" to the form
    and set data-dialog-title / data-dialog-description attributes if needed.
--}}
<div id="confirm-delete-dialog"
     data-dialog
     class="fixed inset-0 z-50 hidden bg-black/50 flex items-center justify-center p-4">
    <div class="dialog-panel">
        <div class="dialog-header">
            <h2 class="dialog-title">Are you absolutely sure?</h2>
            <p class="dialog-description">This action cannot be undone. This data will be permanently deleted.</p>
        </div>
        <div class="dialog-footer">
            <button type="button" data-dialog-close class="btn btn-outline btn-sm">
                Cancel
            </button>
            <button type="button" id="confirm-delete-btn" class="btn btn-destructive btn-sm">
                Delete
            </button>
        </div>
    </div>
</div>
