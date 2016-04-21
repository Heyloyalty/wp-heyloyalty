<?php
/** Loads the WordPress Environment and Template */
require$_SERVER['DOCUMENT_ROOT'].'/wp-blog-header.php';

$stream = detectRequestBody();
$requestBody = stream_get_contents($stream);
$bodyArray = json_decode($requestBody);

if(isset($bodyArray->type) && $bodyArray->type == 'unsubscribe')
{
    $email = $bodyArray->member->fields->email;
    if(isset($email)) {
        $user = get_user_by('email', $email);
    }
    if(isset($user)) {
        update_user_meta($user->ID, 'hl_permission', 'off');
    }
}

function detectRequestBody() {
    $rawInput = fopen('php://input', 'r');
    $tempStream = fopen('php://temp', 'r+');
    stream_copy_to_stream($rawInput, $tempStream);
    rewind($tempStream);

    return $tempStream;
}