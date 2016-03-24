<?php
/**
 * @var array $error_message_a
 * @var \Entity\Comment[] $Comment_a
 * @var array $comment_update_url_a
 * @var array $comment_delete_url_a
 * @var array $comment_user_url_a
 * @var array $comment_write_access_a
 * @var string $comments_html
 */

$json_answer = [];

// On ajoute les erreurs s'il en existe
$json_answer['errors'] = [];
foreach ($error_message_a as $error) {
    $json_answer['errors'][] = $error;
}
$json_answer['errors_exists'] = !empty($json_answer['errors']);

$json_answer['comments_html'] = $comments_html;

$json = json_encode($json_answer);
