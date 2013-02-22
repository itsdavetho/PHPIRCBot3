PHPIRCBot
=========

PHPIRCBot version 3 features proper event handlers, modules (previously: commands), and it's all made by you! I am including absolutely no content in this release, so it's up to you to make it work.
The class simply connects to a server -> channel and nothing more.

PHPIRCBot v3 is currently in beta testing, so if you encounter an error, don't be afraid! Just report it!.

Example usage of class:
```php
<?php
	require_once('class.ircbot.php');

	$irc = new IRCBot();
	$irc->setArg('IRC_SERVER', 'irc.gyrat.in');
	$irc->setArg('IRC_PORT', 6667);
	$irc->setArg('IRC_CHANNEL', '#balls');
	$irc->setArg('IRC_NICK', 'phpircbot3');
	$irc->setArg('IRC_USER', 'phpircbot3');
	$irc->setArg('IRC_OWNER', 'you');

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

	$irc->start();
?>
```

Example module (listModules.php):
```php
<?php
	class listModules {
		public function execute(IRCBot $irc_bot) {
			$module_list = '';
			$modules = $irc_bot->getModules();
			asort($modules);
			foreach($modules as $module) {
				$module_list .= get_class($module) . ', ';
			}
			echo 'Modules: ' . substr($module_list, 0, -2);
		}
	}
?>
```

All modules must be named after their filename. So if your module is to be called "MyVeryFirstSuperDuperCoolUltraAwesomeModule", then your file must be named after it respectively:
MyVeryFirstSuperDuperCoolUltraAwesomeModule.php

It is possible to make it search for your class within the file found, but I just don't really think it's worth it :p

How to use handle an event within a module
==========================================
Simply create a method named after the event you're handling, prefixed with "EVENT_"!
Like this:
```php
<?php
	class wut {
		public function EVENT_PRIVMSG(IRCBot $irc_bot, array $ds = null) {
			if($ds === null)
				$ds = $irc_bot->getResultSet();
			$irc_bot->sendMessage($ds['trail']);
		}
	}
?>
```
