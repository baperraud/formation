<?php
/**
 * @var \Entity\Comment[] $Comment_a
 * @var array $comment_update_url_a
 * @var array $comment_delete_url_a
 * @var array $comment_user_url_a
 * @var array $comment_write_access_a
 */
?>

<?php foreach ($Comment_a as $Comment): ?>
    <fieldset id="commentaire-<?= $Comment['id'] ?>" data-id="<?= $Comment['id'] ?>">
        <legend>
            Posté par
            <strong>
                <?php if ($Comment['owner_type'] == 1): ?>
                    <a href=<?= $comment_user_url_a[$Comment['id']] ?>><?= $Comment['pseudonym'] ?></a>
                <?php else: ?>
                    <?= $Comment['pseudonym'] ?> (visiteur)
                <?php endif; ?>
            </strong>
            le <?= $Comment['Date']->format('d/m/Y à H\hi') ?>
            <?php if ($comment_write_access_a[$Comment['id']]): ?>
                -
                <a href= <?= $comment_update_url_a[$Comment['id']] ?>>Modifier</a> |
                <a href= <?= $comment_delete_url_a[$Comment['id']] ?>>Supprimer</a>
            <?php endif; ?>
        </legend>
        <p class="overflow_hidden"><?= $Comment['contenu'] ?></p>
    </fieldset>
<?php endforeach;