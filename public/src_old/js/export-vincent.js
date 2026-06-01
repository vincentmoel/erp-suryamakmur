$(document).ready(function () {
    $('.btn-export').click(function (e) {
        var url = $(this).attr('data-url');
        var field = $('#field').val();
        var csrfToken = $('meta[name="csrf-token"]').attr('content');

        var btnId = $(this).attr('id');

        toggleLoadingToButton(btnId, $('#' + btnId).html(), 'add');

        $.ajax({
            type: 'POST',
            url: url,
            data: $('#form-export').serialize(),
            xhrFields: {
                responseType: 'blob'
            },
            headers: {
                'X-CSRF-TOKEN': csrfToken
            },
            success: function (response, status, xhr) {
                var filename = xhr.getResponseHeader('Content-Disposition');

                if (filename) {
                    // Extract filename from Content-Disposition header
                    var matches = /filename="([^"]+)"/.exec(filename);
                    filename = (matches != null && matches[1]) ? matches[1] :
                        'downloaded-file';
                } else {
                    filename = 'downloaded-file'; // Fallback filename
                }

                var contentType = xhr.getResponseHeader('Content-Type');
                var blob = new Blob([response], {
                    type: contentType
                });
                var downloadUrl = URL.createObjectURL(blob);

                var a = document.createElement('a');
                a.href = downloadUrl;
                a.download = filename;
                document.body.appendChild(a);
                a.click();

                document.body.removeChild(a);
                URL.revokeObjectURL(downloadUrl);

                toggleLoadingToButton(btnId, $('#' + btnId).html(), 'remove');

                $('#export-modal').modal('toggle');

                showToast('success', 'Export success', 'File has been downloaded');
            },
            error: function (xhr, status, error) {
                toggleLoadingToButton(btnId, $('#' + btnId).html(), 'remove');
                showToast('error', 'Export failed', 'Please try again');
            },
        });
    });

    $(".select2-field").select2({
        placeholder: "Select Field to Export *",
        dropdownParent: $('#export-modal')
    }).on("select2:select", function (evt) {
        var element = evt.params.data.element;
        var $element = $(element);

        $element.detach();
        $(this).append($element);
        $(this).trigger("change");
    });
});