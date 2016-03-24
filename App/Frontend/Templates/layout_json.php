<?php
/**
 * @var array $content_a
 * @var int $code
 * @var array $error_a
 */

$json_answer = [];

$json_answer['code'] = isset($code) ? $code : 0;
if (isset($error_a)) $json_answer['error_a'] = $error_a;
if (isset($content_a)) $json_answer['content_a'] = $content_a;

$json = json_encode($json_answer);