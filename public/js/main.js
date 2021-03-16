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
        }
    )
;

function menuDisplay() {
    var button = document.getElementById('nav-icon')
    var nav = document.getElementById('nav')
    var background = document.getElementById('nav-background')
    button.classList.toggle('nav-icon-toggled')
    background.classList.toggle('nav-background-toggled')
    nav.classList.toggle('nav-displayed')

    var navList = document.getElementsByClassName("nav-item")
    var delay = 80

    for (let i = 0; i < navList.length; i++) {
        setTimeout( function() {
            navList[i].classList.toggle('nav-item-display')
        }, delay)
        delay += 80
    }
}