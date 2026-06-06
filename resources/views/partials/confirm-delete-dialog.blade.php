{{--
    Reusable delete confirmation dialog (AJAX).

    Trigger: any element with class="dt-delete-btn" and data-url="..."
    On confirm: sends DELETE via AJAX, reloads all active datatables,
    shows toast, then closes the dialog.
--}}
<div id="confirm-delete-dialog"
     data-dialog
     class="fixed inset-0 z-50 hidden bg-black/50 flex items-center justify-center p-4">
    <div class="dialog-panel">
        <div class="dialog-header">
            <h2 class="dialog-title">@lang('general.confirm_delete_title')</h2>
            <p class="dialog-description">@lang('general.confirm_delete_message')</p>
        </div>
        <div class="dialog-footer">
            <button type="button" data-dialog-close class="btn btn-outline btn-sm">
                @lang('general.cancel')
            </button>
            <button type="button" id="confirm-delete-btn" class="btn btn-destructive btn-sm">
                @lang('general.delete')
            </button>
        </div>
    </div>
</div>
