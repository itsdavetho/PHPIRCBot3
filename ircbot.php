<?php
    require_once('class.ircbot.php');

    $irc = new IRCBot();
    $irc->toggleLogging();
    $irc->setArg('IRC_SERVER', 'irc.gyrat.in');
    $irc->setArg('IRC_PORT', 6667);
    $irc->setArg('IRC_CHANNEL', '#balls');
    $irc->setArg('IRC_NICK', 'phpircbot3');
    $irc->setArg('IRC_USER', 'phpircbot3');
    $irc->setArg('OWNER', 'orpheus');

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
            $ds = $irc_bot->sendCommand('JOIN :' . $irc_bot->getArg('IRC_CHANNEL'));
        }
    );

    // Auto-join after kick.
    $irc->addHandler('KICK',
        function(IRCBot $irc_bot) {
            $ds = $irc_bot->getResultSet();
            if(preg_match('/' . $irc_bot->getArg('IRC_NICK') . '/i', $ds['args'])) {
                $irc_bot->sendCommand('JOIN :' . $irc_bot->getArg('IRC_CHANNEL'));
                $irc_bot->sendCommand('PRIVMSG '.$irc_bot->getArg('IRC_CHANNEL').' :Sorry for whatever it was I did!');
            }
        }
    );
    $irc->loadModules();


    $irc->start();
?>