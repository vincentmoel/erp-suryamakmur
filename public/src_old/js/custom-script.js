$('.datatable-container').on('click', '.delete-button', function (e) {
    var deleteButton = $(this);

    Swal.fire({
        title: 'Are you sure?',
        text: "You won't be able to revert this!",
        icon: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#3085d6',
        cancelButtonColor: '#d33',
        confirmButtonText: 'Yes, delete it!'
    }).then((result) => {
        if (result.isConfirmed) {
            var form = deleteButton.closest('form');
            var url = form.attr('action');
            
            $.ajax({
                url: url,
                type: 'DELETE',
                headers: {
                    'X-CSRF-TOKEN': jQuery('meta[name="csrf-token"]').attr('content')
                },
                success: function(response) {
                    var table = $('.dataTable').DataTable();

                    table.ajax.reload(function() {
                        // After reload, check if there are no visible rows
                        if (table.page.info().pages > 0 && table.rows().count() === 0) {
                            table.page('first').draw('page');
                        }
                    }, false); 

                    toastr.success(
                        response.data.message,
                        response.data.title, 
                        {
                            showMethod: "fadeIn",
                            hideMethod: "fadeOut",
                            timeOut: 4000,
                            positionClass: "toastr toast-top-center mt-3",
                            containerId: "toast-top-center"
                        }
                    );
                }
            });
        }
    });
});

$('.datatable-container').on('click', '.restore-button', function (e) {
    var restoreButton = $(this);

    Swal.fire({
        title: 'Are you sure?',
        text: "You won't be able to revert this!",
        icon: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#3085d6',
        cancelButtonColor: '#d33',
        confirmButtonText: 'Yes, restore it!'
    }).then((result) => {
        if (result.isConfirmed) {
            restoreButton.closest('form').submit();
        }
    });
});

function toggleLoadingToButton(buttonId, buttonText, action = 'add') {
    if (action === 'add') {
        $("#" + buttonId).prop('disabled', true)
            .html('<span class="me-1 spinner-border spinner-border-sm loading-spinner" role="status" aria-hidden="true"></span>' + buttonText);
    } else if (action === 'remove') {
        $("#" + buttonId).prop('disabled', false)
            .find(".loading-spinner")
            .remove();
    }
}

function sweetAlert(
    button,
    confirmButtonText = 'Yes, delete it!',
    icon = 'warning',
    title = "Are you sure?", 
    message = "You won't be able to revert this!", 
) {
    Swal.fire({
        title: title,
        text: message,
        icon: icon,
        showCancelButton: true,
        confirmButtonColor: '#3085d6',
        cancelButtonColor: '#d33',
        confirmButtonText: confirmButtonText,
    }).then((result) => {
        if (result.isConfirmed) {
            if(button != null)
            {
                button.closest('form').submit();
            }
        }
    });
}

function showToast(condition, title, message)
{
    if(condition == 'error')
    {
        toastr.error(
            message,
            title, {
                showMethod: "fadeIn",
                hideMethod: "fadeOut",
                timeOut: 4000,
                positionClass: "toastr toast-top-center mt-3",
                containerId: "toast-top-center"
            }
        );
    }
    else if(condition == 'success')
    {
        toastr.success(
            message,
            title, {
                showMethod: "fadeIn",
                hideMethod: "fadeOut",
                timeOut: 4000,
                positionClass: "toastr toast-top-center mt-3",
                containerId: "toast-top-center"
            }
        );
    }
}

function formatRupiah(number) {
    return new Intl.NumberFormat('id-ID', {
        style: 'currency',
        currency: 'IDR',
        minimumFractionDigits: 0,
        maximumFractionDigits: 0
    }).format(number);
}

function cleanRupiah(rupiahString) {
    return parseInt(rupiahString.replace(/[^0-9]/g, ''));
}