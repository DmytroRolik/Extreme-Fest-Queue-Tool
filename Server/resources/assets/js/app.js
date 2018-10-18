
/**
 * First we will load all of this project's JavaScript dependencies which
 * includes Vue and other libraries. It is a great starting point when
 * building robust, powerful web applications using Vue and Laravel.
 */

//equire('./bootstrap');
//require('jquery');


$(document).ready(function () {

    // PHOTO PLACE JS
    //---------------
    $('.photo-place.enabled').click(function () {

       var photoName = $(this).data('photo-name');

       if(!$(this).hasClass("photo-place-filled")) {
           $("#" + photoName + "-deleted").val('');
           $("#" + photoName).trigger('click');
       }else{
           $("#" + photoName).val('');
           $(this).find('img').remove();
           $("#" + photoName + "-deleted").val('true');
           $(this).removeClass('photo-place-filled');
           $(this).find('.fa.fa-plus').show();
       }
    });

    $('.input-photo').on('change', function () {

        readURL(this);
    });

    function readURL(input) {

        var photoName = $(input).attr('id');

        if (input.files && input.files[0]) {

            var reader = new FileReader();

            reader.onload = function(e) {

                $(".photo-place[data-photo-name='" + photoName + "'] .photo-holder .fa.fa-plus").hide();
                $(".photo-place[data-photo-name='" + photoName + "']").addClass('photo-place-filled');
                $(".photo-place[data-photo-name='" + photoName + "'] .photo-holder img").remove();
                $(".photo-place[data-photo-name='" + photoName + "'] .photo-holder").append("<img style='vertical-align: middle; display: table-cell' class='photo-place-image' src='"+ e.target.result + "'>");
            }

            reader.readAsDataURL(input.files[0]);
        }
    }
    //---------------
    // PHOTO PLACE JS END

    $('[data-toggle="tooltip"]').tooltip();

});
