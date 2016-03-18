$(document).ready(function () {

    var $body = $("body"),
        $window = $(window),
        $comments_rank = 1,
        $load_active = true,
        $comments_container = $('#comments_container');


    $(document).on({
        ajaxStart: function () {
            setTimeout(function () {
                if ($.active > 0) $body.addClass("loading");
            }, 250);

        },
        ajaxStop: function () {
            $body.removeClass("loading");
        }
    });


    /* Requête AJAX pour l'envoi du formulaire (poster un commentaire) */
    $(document).on("submit", ".insert_comment_form", function () {
        var $this = $(this);

        $.post(
            $this.data('ajax'),
            {
                pseudonym: $('#pseudonym', $this).val(),
                email: $('#email', $this).val(),
                contenu: $('#contenu', $this).val(),
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

                    var $last_comment;
                    // On génère les nouveaux commentaires
                    var $comments_a = data.comments.reverse();
                    for (i = 0; i < $comments_a.length; i++) {
                        $last_comment = news_buildCommentHTML($comments_a[i]);
                        $comments_container.prepend(
                            $last_comment
                        );
                    }


                    // On centre l'affichage sur le dernier commentaire inséré
                    var viewportHeight = $window.height(),
                        elHeight = $last_comment.height(),
                        elOffset = $last_comment.offset();
                    $('html, body').animate({scrollTop: (elOffset.top + (elHeight / 2) - (viewportHeight / 2) )}, 300);
                }
            },
            'json'
        );

        removeFlash();

        return false;
    });


    /* Requête AJAX pour l'affichage des anciens commentaires */
    var timer;
    $window.scroll(function () {
        clearTimeout(timer);
        timer = setTimeout(function () {


            if ($load_active) {

                if (news_isOnScreen($comments_container.find('fieldset:last'))) {

                    $.post(
                        $comments_container.data('load_old'),
                        {
                            rang: $comments_rank
                        },
                        function (data) {

                            // On génère les anciens commentaires
                            for (var i = 0; i < data.comments.length; i++) {
                                $comments_container.append(news_buildCommentHTML(data.comments[i]));
                            }

                            // On incrémente le rang des commentaires à afficher
                            $comments_rank = $comments_rank + 1;

                            /* Si l'on a renvoyé moins de 15 commentaires,
                             alors il n'y en a plus à charger */
                            if (data.comments.length < $comments_container.data('limit'))
                                $load_active = false;
                        },
                        'json'
                    );

                }
            }

        }, 250);
    });


    /* Requête AJAX de rafraichissment */
    window.setInterval(function () {

        // On affiche les nouveaux commentaires
        $.post(
            $comments_container.data('load_new'),
            {
                last_comment: $comments_container.find('fieldset:first').data('id')
            },
            function (data) {
                // On génère les nouveaux commentaires
                var $comments_a = data.comments.reverse();
                for (var i = 0; i < $comments_a.length; i++) {
                    $comments_container.prepend(news_buildCommentHTML($comments_a[i]));
                }
            },
            'json'
        );

        // On efface les commentaires ayant été supprimé
        var $comment_a = $comments_container.find('fieldset'),
            $comment_id_a = [];
        for (var i = 0; i < $comment_a.length; i++) {
            $comment_id_a.push($($comment_a[i]).data('id'));
        }
        $.post(
            $comments_container.data('get_deleted'),
            {
                comments: $comment_id_a
            },
            function (data) {
                // On retire les commentaires qui ont été supprimé
                for (var i = 0; i < data.deleted.length; i++) {
                    $comments_container.find("[data-id='" + data.deleted[i] + "']").remove();
                }
            },
            'json'
        );

    }, 5000);


})
;

function news_buildCommentHTML(comment) {
    var user = null;
    if (comment.owner_type == 1)
        user = $('<a></a>')
            .attr('href', comment.user)
            .text(comment.pseudonym);
    else
        user = comment.pseudonym + ' (visiteur)';

    var edit_button = '';
    var delete_button = '';
    if (comment.write_access) {
        edit_button = $('<a></a>')
            .attr('href', comment.update)
            .text('Modifier');
        delete_button = $('<a></a>')
            .attr('href', comment.delete)
            .text('Supprimer');
    }

    return $('<fieldset></fieldset>')
        .attr('id', 'commentaire-' + comment.id)
        .attr('data-id', comment.id)
        .append(
            $('<legend></legend>')
                .append(
                    'Posté par ',
                    $('<strong></strong>')
                        .append(
                            user
                        ),
                    ' le ' + comment.date,
                    (comment.write_access) ? ' - ' : '',
                    edit_button,
                    (comment.write_access) ? ' | ' : '',
                    delete_button
                ),
            $('<p></p>')
                .addClass('overflow_hidden')
                .text(comment.contenu)
        );

}

function news_isOnScreen(comment) {
    var element = comment.get(0);
    var bounds = element.getBoundingClientRect();
    return bounds.top < window.innerHeight && bounds.bottom > 0;
}

function removeFlash() {
    var $flash = $('#flash_message');
    if ($flash.length) $flash.remove();
}
