(function () {

    $(document).ready(function () {

        $('.collapse').each(function () {
            $(this).collapse({toggle: $(this).hasClass("collapse-initially-open")});
        });

        var newindex = 0;

        $('.add-player').on('click', function (evt) {
            var proto,
                clone,
                oldindex, input;

            proto = $('.player-condition.original');
            clone = proto.clone();

            input = clone.find('input[type=text]');
            oldindex = parseInt(input.attr('name').match(/player\[(\d+)\]/)[1]);
            newindex = newindex ? newindex + 1 : oldindex + 1;
            $.each(clone.find(".form-control[name]"), function () {
                var a = "[" + oldindex + "]",
                    b = "[" + newindex + "]";
                $(this).attr("name", $(this).attr("name").replace(a, b));
            });
            clone.find('.player-name > .row').not(":first").remove();
            clone.removeClass('original').insertBefore($(this));
            input.val('').focus();
            evt.stopPropagation();
            evt.preventDefault();
        });

        $('form').on('click', '.add-alias', function () {
            var d = $(this).closest('.row.original'),
                c = d.clone().removeClass('original');
            c.find('input').val("");
            c.insertAfter(d);
        });

        $('form').on('click', '.remove-alias', function () {
            var p = $(this).closest('.row');
            if (!p.hasClass('original')) {
                p.remove();
            }
        });

        $('form').on('click', '.remove-condition', function () {
            var condition = $(this).closest('.player-condition');
            condition.hasClass('original') || condition.remove();
        });
    });
}());
