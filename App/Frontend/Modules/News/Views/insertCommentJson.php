<?php
/**
 * @var array $error_message_a
 * @var \Entity\Comment[] $Comment_a
 * @var array $comment_update_url_a
 * @var array $comment_delete_url_a
 * @var array $comment_user_url_a
 * @var array $comment_write_access_a
 */

$json_answer = [];

// On ajoute les erreurs s'il en existe
$json_answer['errors'] = [];
foreach ($error_message_a as $error) {
    $json_answer['errors'][] = $error;
}
$json_answer['errors_exists'] = !empty($json_answer['errors']);

// On ajoute les derniers commentaires
$json_answer['comments'] = [];
foreach ($Comment_a as $Comment) {
    $comment_a = [];
    $comment_a['id'] = $Comment['id'];
    $comment_a['date'] = $Comment['Date']->format('d/m/Y à H\hi');
    $comment_a['pseudonym'] = $Comment['pseudonym'];
    $comment_a['contenu'] = $Comment['contenu'];
    $comment_a['owner_type'] = $Comment['owner_type'];
    $comment_a['update'] = $comment_update_url_a[$Comment['id']];
    $comment_a['delete'] = $comment_delete_url_a[$Comment['id']];
    $comment_a['user'] = $comment_user_url_a[$Comment['id']];
    $comment_a['write_access'] = $comment_write_access_a[$Comment['id']];
    $json_answer['comments'][] = $comment_a;
}

$json = json_encode($json_answer);
