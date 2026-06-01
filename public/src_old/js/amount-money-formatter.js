$(document).ready(function() {
    // set the default value to the old value if it exists, otherwise set it to "Rp "
    var currentValue = $('#amount').val();

    if (currentValue === null || currentValue === '') {
        $('#amount').val("Rp ");
    }

    $(document).on('input', '#amount', function(e) {
        var $input = $(this);
        var inputValue = $input.val();

        // only allow numeric characters and remove non-numeric characters
        var numericValue = inputValue.replace(/[^0-9]/g, '');

        // if the input value is empty, set it back to the default value
        if (numericValue === '') {
            $input.val("Rp ");
        } else {
            // format the numeric value as Indonesian Rupiah
            var formattedAmount = formatRupiah(numericValue);

            // set the formatted value back to the input field
            $input.val(formattedAmount);
        }

        // prevent non-numeric characters from being entered
        e.preventDefault();
    });
});

function formatRupiah(angka) {
    if (angka === '') {
        return 'Rp';
    } else {
        var reverse = angka.toString().split('').reverse().join('');
        var ribuan = reverse.match(/\d{1,3}/g);
        var formatted = ribuan.join('.').split('').reverse().join('');
        return 'Rp ' + formatted;
    }
}