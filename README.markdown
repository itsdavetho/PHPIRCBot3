PHPIRCBot
=========

PHPIRCBot is now better than ever. It features an easy to use module system, allowing you to create event handlers (for messages from the server) on the fly. PHPIRCBot allows you to make very intricate IRC bots with ease. 

There are no included modules with PHPIRCBot, you must make your own. See the examples below. 

Example usage may be found in ircbot.php

Example modules may be found in modules folder.

Module names must correspond to the file name. That means if your class name is doSomething, then your filename should be doSomething.php

Event handling in modules
=========================
To handle events within modules, all you need to do is create a new function named EVENT_COMMAND.
For example, EVENT_PRIVMSG.
The example script below "echos" every message sent. 
```php
<?php
	class wut {
		public function EVENT_PRIVMSG(IRCBot $irc_bot) {
			$ds = $irc_bot->getResultSet();
			$irc_bot->sendMessage($ds['trail']);
		}
	}
?>
```
