<?php
/**
 * @var array $comment_a
 */

$json_answer = [];

$json_answer['deleted'] = [];

foreach ($comment_a as $deleted_comment)
    $json_answer['deleted'][] = $deleted_comment;

$json = json_encode($json_answer);