<?php
    require_once('class.ircbot.php');

    $irc = new IRCBot();
    $irc->setArg('IRC_SERVER', 'irc.rizon.net');
    $irc->setArg('IRC_PORT', 6660);
    $irc->setArg('IRC_CHANNEL', '#phpircbot');
    $irc->setArg('IRC_NICK', 'b_' . mt_rand(1, 10000));
    $irc->setArg('IRC_USER', 'phpircbot3');
    $irc->setArg('OWNER', 'hello__');
    $irc->setArg('verbose', true); // all data echoed during module execution will be sent as a message
    $irc->setArg('auto-reconnect', true); // reconnects to server upon disconnection.
    $irc->start();
?>
