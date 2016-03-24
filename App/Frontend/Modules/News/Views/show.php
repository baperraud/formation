<?php
/**
 * @var \Entity\News $News
 * @var \OCFram\Form $Form
 * @var array $comment_news_url_a
 * @var string $news_user_url
 * @var array $json_comments_url_a
 * @var string $nombre_commentaires
 * @var string $comments_html
 */

use OCFram\Session;

?>

<p>Par <em><a href="<?= $news_user_url ?>"><?= htmlspecialchars($News['auteur']) ?></a></em>,
    le <?= $News['Date_ajout']->format('d/m/Y à H\hi') ?></p>

<h2 class="overflow_hidden"><?= htmlspecialchars($News['titre']) ?></h2>

<p class="overflow_hidden"><?= htmlspecialchars($News['contenu']) ?></p>

<?php if ($News['Date_ajout'] != $News['Date_modif']): ?>
    <p style="text-align: right;">
        <small><em>Modifiée le <?= $News['Date_modif']->format('d/m/Y à H\hi') ?></em></small>
    </p>
<?php endif; ?>

<div id="insert_comment_form_container_top" class="insert_comment_form_container">
    <h3>Insérer un commentaire :</h3>
    <form id="insert_comment_form_top" class="insert_comment_form" action="<?= $comment_news_url_a['html'] ?>" data-id="Top" data-ajax="<?= $comment_news_url_a['json'] ?>"
          method="post">
        <p>
            <?= $Form->createView() ?>

            <input type="submit" value="Commenter"/>

        </p>
    </form>
</div>

<div id="comments_container" data-load="<?= $json_comments_url_a['load'] ?>" data-get_deleted="<?= $json_comments_url_a['deleted'] ?>" data-limit="<?= $nombre_commentaires ?>">

    <?php if ($comments_html == ''): ?>
        <p id="no_comment_alert">Aucun commentaire n'a encore été posté. Soyez le premier à en laisser un !</p>
    <?php else: ?>
        <?= $comments_html ?>
    <?php endif; ?>

</div>

<div id="insert_comment_form_container_bottom" class="insert_comment_form_container">
    <h3>Insérer un commentaire :</h3>
    <form id="insert_comment_form_bottom" class="insert_comment_form" action="<?= $comment_news_url_a['html'] ?>" data-id="Bottom" data-ajax="<?= $comment_news_url_a['json'] ?>"
          method="post">
        <p>
            <?= $Form->createView() ?>

            <input type="submit" value="Commenter"/>
        </p>
    </form>
</div>

<div class="spinning_loading"></div>