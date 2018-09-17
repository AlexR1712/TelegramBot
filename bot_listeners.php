<?php

use App\Event;

// Filter User if spammer

Event::listen('text', function ($text, $bot, $update) {
    if ($text === 'ping') {
        $bot->sendMessage($update['message']['chat']['id'], 'pong!');
    }
});

Event::listen('new_chat_member', function ($member, $bot, $update) {
    echo 'Event New Member fired!\n';
    $chat_id = $update['message']['chat']['id'];
    $user_id = $update['message']['new_chat_member']['id'];

    $str = $update['message']['new_chat_member']['first_name'];
    $re = '/\p{Han}+/miu';
    preg_match_all($re, $str, $matches, PREG_SET_ORDER, 0);

    if (count($matches) > 0) {
        // if the user has chinese characters the bot kick that user
        // $until = time()+(366 * 24 * 60 * 60);
        $res = $bot->kickChatMember($chat_id, $user_id);
    }
    // Delete join message...
    $bot->deleteMessage($chat_id, $update['message']['message_id']);
});
