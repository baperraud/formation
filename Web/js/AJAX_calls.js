$(document).ready(function () {

    function escapeHtml(unsafe) {
        return unsafe
            .replace(/&/g, "&amp;")
            .replace(/</g, "&lt;")
            .replace(/>/g, "&gt;")
            .replace(/"/g, "&quot;")
            .replace(/'/g, "&#039;");
    }

    var $body = $("body");

    $(document).on({
        ajaxStart: function () {
            $body.addClass("loading");
        },
        ajaxStop: function () {
            $body.removeClass("loading");
            if ($('#flash_message').length) $('#flash_message').remove();
        }
    });

    $(document).on("submit", ".insert_comment_form", function () {
        var $this = $(this),
            $comments_container = $('#comments_container');

        $.post(
            $comments_container.data('json'),
            {
                pseudonym: $('#pseudonym' + $this.data('id')).val(),
                email: $('#email' + $this.data('id')).val(),
                contenu: $('#contenu' + $this.data('id')).val(),
                last_comment: $comments_container.data('last_comment')
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

                    // On génère les nouveaux commentaires
                    for (i = 0; i < data.comments.length; i++) {
                        $comments_container.prepend(
                            '<fieldset id="commentaire-' + data.comments[i]['id'] + '">' +
                            '<legend>' +
                            'Posté par <strong>' +
                            (data.comments[i]['owner_type'] == 1
                                ? '<a href="' + data.comments[i]['user'] + '">' + escapeHtml(data.comments[i]['pseudonym']) + '</a>' : escapeHtml(data.comments[i]['pseudonym']) + ' (visiteur)') +
                            '</strong>' +
                            ' le ' + data.comments[i]['date'] +
                            (data.comments[i]['write_access'] == true ? '- <a href="' + data.comments[i]['update'] + '">Modifier</a> | <a href="' + data.comments[i]['delete'] + '">Supprimer</a>' : '') +
                            '</legend>' +
                            '<p class="overflow_hidden">' + escapeHtml(data.comments[i]['contenu']) + '</p>' +
                            '</fieldset>'
                        );
                    }

                    // On update l'id du dernier commentaire inséré
                    $comments_container.data('last_comment', data.comments[0]['id']);

                    // On centre l'affichage sur le dernier commentaire inséré
                    var viewportHeight = jQuery(window).height(),
                        last_comment = $('#comments_container fieldset:first'),
                        elHeight = last_comment.height(),
                        elOffset = last_comment.offset();
                    jQuery(window).scrollTop(elOffset.top + (elHeight / 2) - (viewportHeight / 2));
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

