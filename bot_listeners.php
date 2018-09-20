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
    $re = '/[\x{3041}-\x{3096}\x{30A0}-\x{30FF}\x{3400}-\x{4DB5}\x{4E00}-\x{9FCB}\x{F900}-\x{FA6A}\x{2E80}-\x{2FD5}\x{FF5F}-\x{FF9F}\x{3000}-\x{303F}\x{31F0}-\x{31FF}\x{3220}-\x{3243}\x{3280}-\x{337F}]|\p{Han}+/miu';
    preg_match_all($re, $str, $matches, PREG_SET_ORDER, 0);

    if (count($matches) > 0) {
        // if the user has chinese characters the bot kick that user
        // $until = time()+(366 * 24 * 60 * 60);
        $res = $bot->kickChatMember($chat_id, $user_id);
    } else {
        // Say Hi to new member
        $first_name = $update['message']['new_chat_member']['first_name'];
        $username = ($update['message']['new_chat_member']['username']) ? '( @'.$update['message']['new_chat_member']['username'].' )' : '';
        $welcome_text = "📢 Bienvenido/a <b>$first_name</b> $username a <a href='https://telegram.me/PHP_Ve'>PHP.ve</a>, te invitamos a que leas la <a href='http://telegra.ph/PHPve-11-24'>Descripción y Normas del Grupo</a>";
        $chat_id = $update['message']['chat']['id'];

        $bot->sendMessage($chat_id, $welcome_text, [
            'parse_mode' => 'HTML',
        ]);
    }
    // Delete join message...
    $bot->deleteMessage($chat_id, $update['message']['message_id']);
});
