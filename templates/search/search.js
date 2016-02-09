(function () {
    $(document).ready(function () {
        $('.add-player').on('click', function (evt) {
            var proto,
                clone;

            proto = $($('.player-condition')[0]);
            clone = proto.clone();

            clone.removeClass('original');
            clone.insertBefore($(this));
            clone.find('input[type=text]').val('').focus();
            evt.stopPropagation();
            evt.preventDefault();
        });

        $(document).on('click', '.remove-condition', function () {
            var condition = $(this).closest('.player-condition');
            condition.hasClass('original') || condition.remove();
        });
    });
}());
