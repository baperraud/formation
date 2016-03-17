$(document).ready(function () {

    var $body = $("body");

    $(document).on({
        ajaxStart: function () {
            $body.addClass("loading");
        },
        ajaxStop: function () {
            $body.removeClass("loading");
            var $flash = $('#flash_message');
            if ($flash.length) $flash.remove();
        }
    });

    $(document).on("submit", ".insert_comment_form", function () {
        var $this = $(this),
            $comments_container = $('#comments_container');

        $.post(
            $this.data('ajax'),
            {
                pseudonym: $('#pseudonym',$this).val(),
                email: $('#email',$this).val(),
                contenu: $('#contenu',$this).val(),
                last_comment: $comments_container.find('fieldset:first').data('id')
            },
            function (data) {
                if (data.errors_exists) {
                    $this.children('p.error').remove();
                    for (var i = 0; i < data.errors.length; i++) {
                        $this.append('<p class="error">' + data.errors[i] + '</p>');
                    }
                } else {
                    // On clean le formulaire et les messages d'erreur
                    $this.find("input[type=text], input[type=email], textarea").val("");
                    $this.children('p.error').remove();

                    // On retire le message 'Aucun commentaire...' si c'est le 1er
                    if ($comments_container.data('last_comment') == 0)
                        $('#no_comment_alert').remove();

                    var $last_comment = false;
                    // On génère les nouveaux commentaires
                    for (i = 0; i < data.comments.length; i++) {
                        $last_comment = news_buildCommentHTML(data.comments[i]);
                        $comments_container.prepend(
                            $last_comment
                        );
                    }

                    // On update l'id du dernier commentaire inséré
                    //$comments_container.data('last_comment', data.comments[0]['id']);

                    var $window = $(window);

                    // On centre l'affichage sur le dernier commentaire inséré
                    var viewportHeight = $window.height(),
                        //last_comment = $('#comments_container fieldset:first'),
                        elHeight = $last_comment.height(),
                        elOffset = $last_comment.offset();
                    //$window.animate({'scrollTop' : (elOffset.top + (elHeight / 2) - (viewportHeight / 2) ) },300);
                    $('html, body').animate({scrollTop : (elOffset.top + (elHeight / 2) - (viewportHeight / 2) )  },300);
                }
            }
            ,
            'json'
        );

        return false;
    })
    ;

})
;

function news_buildCommentHTML(comment) {
    var user = null;
    if (comment.owner_type == 1)
       user = $('<a></a>')
                        .attr('href',comment.user)
                        .text(comment.pseudonym);
    else
       user = comment.pseudonym+' (visiteur)';

    var edit_button = '';
    var delete_button = '';
    if (comment.write_access) {
        edit_button = $('<a></a>')
            .attr('href',comment.update)
            .text('Modifier');
        delete_button = $('<a></a>')
            .attr('href',comment.delete)
            .text('Supprimer');
    }

    return $('<fieldset></fieldset>')
        .attr('id','commentaire-'+comment.id)
        .attr('data-id',comment.id)
        .append(
            $('<legend></legend>')
                .append(
                    'Posté par ',
                    $('<strong></strong>')
                        .append(
                            user
                        ),
                    ' le '+comment.date,
                    (comment.write_access)?' - ':'',
                    edit_button,
                    (comment.write_access)?' | ':'',
                    delete_button
                ),
            $('<p></p>')
                .addClass('overflow_hidden')
                .text(comment.contenu)
        );
        //'<fieldset id="commentaire-' + data.comments[i]['id'] + '">' +
        //'<legend>' +
        //'Posté par <strong>' +
        //(data.comments[i]['owner_type'] == 1
        //    ? '<a href="' + data.comments[i]['user'] + '">' + escapeHtml(data.comments[i]['pseudonym']) + '</a>' : escapeHtml(data.comments[i]['pseudonym']) + ' (visiteur)') +
        //'</strong>' +
        //' le ' + data.comments[i]['date'] +
        //(data.comments[i]['write_access'] == true ? '- <a href="' + data.comments[i]['update'] + '">Modifier</a> | <a href="' + data.comments[i]['delete'] + '">Supprimer</a>' : '') +
        //'</legend>' +
        //'<p class="overflow_hidden">' + escapeHtml(data.comments[i]['contenu']) + '</p>' +
        //'</fieldset>'
    //);

}

