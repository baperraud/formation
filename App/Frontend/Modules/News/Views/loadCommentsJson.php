<?php
/**
 * @var string $comments_html
 * @var int $comments_count
 */

$json_answer = [];

$json_answer['comments_html'] = $comments_html;
$json_answer['comments_count'] = $comments_count;

$json = json_encode($json_answer);