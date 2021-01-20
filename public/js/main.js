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
        $('.message .content')
            .removeClass('hidden');
        ;
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