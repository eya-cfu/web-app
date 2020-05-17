

var ww= $(window).width();

$(function () {

    if (ww <= 670)
    {
        $('.hideable').hide();
        $('div.sideBar').addClass('sidebarShrink');
        $('hr#mSpecialBar').addClass('hrShrink');
        $('li#active').addClass('sideBarBar');
        $('li ul li').hide();
    }

})



    $('#sidebarCollapse').click(function () {
        $('.hideable').toggle();
        $('div.sideBar').toggleClass('sidebarShrink');
        $('hr#mSpecialBar').toggleClass('hrShrink');
        $('li#active').toggleClass('sideBarBar');
        $('li ul li').toggle();
    })

    $('[href="#authSubmenu"]').click(function () {
        $('.hideable').show();
        $('div.sideBar').removeClass('sidebarShrink');
        $('hr#mSpecialBar').removeClass('hrShrink');
        $('li#active').removeClass('sideBarBar');
        $('li ul li').show();
    })



   $('.Remove').click(function () {

       let r = $(this);

       const url = $(this).attr('href');



       $('.confirmed').click(function () {
           $('.modal').modal('hide');

           axios.get(url).then(function (response) {
                   console.log(response);
                   location.reload();
               }
           );

          // r.parent().parent().remove();

       })

   })

$('input.toggPwd').focus(function () {

    $(this).attr('type', 'text');
})

$('input.toggPwd').focusout(function () {

    $(this).attr('type', 'password');
})


/*
$('a#needPwd').click(function () {

})
*/

/*modification Modal*/

$('.Update').click(function () {

    const url = $(this).attr('data-target');
        $.get(url, function (data) {
            console.log(data);
           $('.modal-form').html(data);
            $('.modificationModal').modal();




            $('form').submit(function (e) {

                if (    $('input#boulangerie_matricule').val() < 1000   || $('input#boulangerie_matricule').val() > 9999)
                {
                    e.preventDefault();
                    $('form span.error').text("Veuillez entrer 4 chiffres").show();
                }


            })
        })


})



$('td a.description').click(function () {



    const url = $(this).attr('data-target');

    $.get(url, function (data) {
        $('.blackback').fadeIn(500);
        $('.compositionsBox').html(data);
    })

})

$('div.blackback i.fa-times').click(function () {

    $('.blackback').hide();
})



if ( window.history.replaceState ) {
    window.history.replaceState( null, null, window.location.href );
}

