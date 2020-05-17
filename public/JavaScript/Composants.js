
$('a.Comp').click(function () {

   $('select#FormControlSelect').on('click', function () {

      if ($(this).val() == 'Autre') {

          $('input.EnableMe').prop("disabled", false);
          $('div.newComp').show();


      }
      else{
          $('input.EnableMe').prop("disabled", true);
          $('div.newComp').hide();
      }
   });


})

var myarray = [] ;

$('form').submit(function (event) {
    event.preventDefault();


        var val = $('select#FormControlSelect').val()  ;

        if(val == 'Autre')
        {
           var val = $('input#FormNameInput').val();
           var unite = $('select#FormUnitySelect').val();
           var id = $('input#FormIdInput').val();

          var composant = { 'idComposant' : id , 'nomComp': val , 'unite': unite}
            const urlc = $('button.btn-primary').attr('data-target');

            console.log(urlc);

            $.ajax({
                method: 'POST',
                url: urlc,
                dataType: 'json',
                data: JSON.stringify(composant)

            }).done(function(msg){
                console.log(msg);
            }).fail(function () {
                 alert('Requête échoué, cet ID est déja utilisé');
            })


        }else{
            var id = $('select#FormControlSelect').find(':selected').attr('data-id');
        }

        var quantity = $('input.quantite').val();
        $('div.empty').addClass('display');
        $('table').removeClass('display');

        $('table.table-hover tbody').append('<tr> <td>' +val +'</td> <td>'+quantity+'</td> <td> <a href="#"><i class="fas fa-times-circle" style="color: firebrick"></i></a> </td></tr>');



        var composition = {'idComposant': id , 'nomComp':val , 'quantiteComp' : quantity}
        myarray.push(composition);

        $('.modal').modal('hide');

        $('i.fa-times-circle').click(function () {

            var todelete  = $(this).parent().parent().parent().text().split(' ');
            todelete.shift();

            var i;
            for(i=0; i < myarray.length ; i++) {

             if(myarray[i].nomComp == todelete[0] && myarray[i].quantiteComp == todelete[1])
                 myarray.splice(i,1);
          }



            $(this).parent().parent().parent().remove();
        }) ;




        $('button.submitButt').click(function () {

            const url = $(this).attr('data-target');
            const urlNext =$(this).attr('data-next');

            $.ajax({
                method: 'POST',
                url: url,
                dataType: 'json',
                data: JSON.stringify(myarray)

        }).done(function(msg){
                console.log(msg);
                alert('Envoyé avec success');
                location.replace(urlNext);
            })
        })

})





