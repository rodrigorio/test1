$(document).ready(function(){
    var itemclone, idx;
    $("#unidadesSinAsociar, #unidadesAsociadas").sortable({
        start: function(event, ui) {
            //create clone of current seletected li
            itemclone = $(ui.item).clone();
            //get current li index position in list
            idx = $(ui.item).index();
            //If first li then prepend clone on first position
            if (idx == 0) {
                itemclone.css('opacity', '0.5');
                $(this).prepend(itemclone);
            }
            //Else Append Clone on its original position
            else {
                itemclone.css('opacity', '0.7');
                $(this).find("li:eq(" + (idx - 1) + ")").after(itemclone);
            }

        },
        change: function(event, ui) {
            //While Change event set clone position as relative
            $(this).find("li:eq(" + idx + ")").css('position', 'relative');

        },
        stop: function() {
            //Once Finish Sort, remove Clone Li from current list
            $(this).find("li:eq(" + idx + ")").remove();
        },
        connectWith: ".connectedSortable"
    }).disableSelection();
});