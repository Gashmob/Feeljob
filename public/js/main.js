$(document)
    .ready(function () {
        $('.ui.menu .ui.dropdown').dropdown({
            on: 'hover'
        });
        $('.ui.menu a.item')
            .on('click', function () {
                $(this)
                    .addClass('active')
                    .siblings()
                    .removeClass('active')
                ;
            })
        ;
        $('.container .ui.dropdown').dropdown();
        $('.ui.checkbox')
            .checkbox()
        ;
        $('.menu .item')
            .tab()
        ;
        $('.ui.accordion')
            .accordion()
        ;
        $('.ui.form .ui.dropdown')
            .dropdown()
        ;
        $('.dimmable.image').dimmer({
            on: 'hover'
        });
        if ($('.message .content div').html().trim()) $('.message').removeClass('hidden');
        $('.message .close')
            .on('click', function () {
                $(this)
                    .closest('.message')
                    .transition('fade')
                ;
            })
        ;
    })
;