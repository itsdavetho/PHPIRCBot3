<?php
    require_once('class.ircbot.php');

    $irc = new IRCBot();
    $irc->toggleLogging();
    $irc->setArg('IRC_SERVER', 'liptoirc.no-ip.info');
    $irc->setArg('IRC_PORT', 4378);
    $irc->setArg('IRC_CHANNEL', '#fuckass');
    $irc->setArg('IRC_NICK', 'phpircbot');
    $irc->setArg('IRC_USER', 'phpircbot');
    $irc->setArg('OWNER', 'orpheus');
    $irc->setArg('verbose', true); // all data echoed during module execution will be sent as a message
    $irc->setArg('auto-reconnect', true); // reconnects to server upon disconnection.

    // Output any PRIVMSG's
    $irc->addHandler('PRIVMSG',
        function(IRCBot $irc_bot) {
            $ds = $irc_bot->getResultSet();
            echo '['.date('h:i').'] <'.trim($ds['args']).' '.trim($ds['username']).'> ' . trim($ds['trail']) . PHP_EOL;
        }
    );

    // Join channel upon connection
    $irc->addHandler('001',
        function(IRCBot $irc_bot) {
            $irc_bot->sendCommand('JOIN :' . $irc_bot->getArg('IRC_CHANNEL'));
        }
    );

    // Auto-join after kick.
    $irc->addHandler('KICK',
        function(IRCBot $irc_bot) {
            $ds = $irc_bot->getResultSet();
            if(preg_match('/' . $irc_bot->getArg('IRC_NICK') . '/i', $ds['args'])) {
                $irc_bot->sendCommand('JOIN :' . $irc_bot->getArg('IRC_CHANNEL'));
                $irc_bot->sendCommand('PRIVMSG '.$irc_bot->getArg('IRC_CHANNEL').' :Sorry.');
            }
        }
    );


    $irc->start();
?>
