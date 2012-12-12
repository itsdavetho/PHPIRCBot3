<?php
	require_once('class.ircbot.php');

	$irc = new IRCBot();
	$irc->setArg('IRC_SERVER', 'irc.gyrat.in');
	$irc->setArg('IRC_PORT', 6667);
	$irc->setArg('IRC_CHANNEL', '#balls');
	$irc->setArg('IRC_NICK', 'phpircbot3');
	$irc->setArg('IRC_USER', 'phpircbot3');
	$irc->setArg('IRC_OWNER', 'orpheus');

	$irc->addHandler('PRIVMSG',
		function(IRCBot $irc_bot) {
			$ds = $irc_bot->getResultSet();
			echo '['.date('h:i').'] <'.trim($ds['args']).' '.trim($ds['username']).'> ' . trim($ds['trail']) . PHP_EOL;
		}
	);
	$irc->addHandler('001',
		function(IRCBot $irc_bot) {
			$ds = $irc_bot->send_cmd('JOIN :' . $irc_bot->getArg('IRC_CHANNEL'));
		}
	);
	$irc->loadModules();


	$irc->start();
?>