$(function() {
    $(document).on('click', '#submit', function (e) {
        let startDate = new Date($("#start_date").val());
        let endDate = new Date($("#end_date").val());
        let currentDate = new Date();

        let errorMessages = [];

        if (startDate > endDate || endDate > currentDate) {
            errorMessages.push('Wrong dates');
        }

        if (0 !== errorMessages.length) {
            e.preventDefault();
            $('#errors').html(errorMessages.join(' '));
        }
    });
})