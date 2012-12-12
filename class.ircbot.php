<?php
	/*
	 * @name		IRCBot
	 * @description	A PHP class that can connect to IRC.
	 * @author		xxOrpheus
	 * @version		3.0
	 * RELEASE NOTES:
	 *	This is currently in beta testing. Don't be alarmed if you encounter a bug, just report it!
	 */

	session_start();
	chdir(dirname(__FILE__));
	date_default_timezone_set('America/Vancouver');

	class IRCBot {
		protected $IRC_SOCKET;
		protected $IRC_DATA;
		protected $IRC_DATA_ARGS;

		protected $IRC_ARGS = array();

		protected $IRCBOT_MODULES = array();

		protected $EVENT_HANDLERS;

		/* 
		 * @desc The destructor. Can be sent an array, which will fill the IRC arguments.
		 * @param array $args IRC arguments. Channel, server, port etc all can be set here..
		 *
		 */
		public function IRCBot(array $args = array()) {
			if(count($args) > 0)
				$this->IRC_ARGS = array_merge($args, $this->IRC_ARGS);
			else {
				$default = array('IRC_PORT' => 6667, 'IRC_NICK' => 'PHPIRCBot', 'IRC_USER' => 'PHPIRCBot', 'OWNER' => 'PHPIRCBot');
				$this->IRC_ARGS = array_merge($default, $this->IRC_ARGS);
			}
		}

		public function start() {
			$this->IRC_SOCKET = @fsockopen($this->getArg('IRC_SERVER'), intval($this->getArg('IRC_PORT')), $SOCK_ERR_NUM, $SOCK_ERR_STR);
			if(!$this->IRC_SOCKET)
				Throw new Exception($SOCK_ERR_STR);
			$this->send_cmd('USER '.$this->getArg('IRC_USER').' 0 * ' . $this->getArg('IRC_USER'));
			$this->send_cmd('NICK '.$this->getArg('IRC_NICK'));
			while($this->IRC_SOCKET) {
				$this->IRC_DATA = fgets($this->IRC_SOCKET, 1024);
				$this->IRC_DATA_ARGS = $this->parse_message($this->IRC_DATA);
				$this->handleCommand($this->IRC_DATA_ARGS['command']);
				if(substr($this->IRC_DATA_ARGS['trail'], 0, 1) == '!') {
					ob_start();
					$this->CURRENT_COMMAND = preg_replace('/(\s*)([^\s]*)(.*)/', '$2', $this->IRC_DATA_ARGS['trail']);
					$this->handleModule(substr($this->CURRENT_COMMAND, 1));
					$data = ob_get_clean();
					if(!empty($data)) {
						$data = explode(PHP_EOL, $data);
						foreach($data as $line) {
							$this->sendMessage($line);
						}
					}
				}
			}
		}

		public function getResultSet() {
			return $this->IRC_DATA_ARGS;
		}

		public function send_cmd($cmd) {
			if(!$this->IRC_SOCKET)
				Throw new Exception('No connection opened');
			fwrite($this->IRC_SOCKET, $cmd . "\r\n");
			fflush($this->IRC_SOCKET);

			return true;
		}
		public function sendMessage($msg) { $this->send_cmd('PRIVMSG ' . $this->IRC_DATA_ARGS['args'] . ' :' . $msg); }

		public function parse_message($str) {
			preg_match('/^(?:[:@]([^\\s]+) )?([^\\s]+)(?: ((?:[^:\\s][^\\s]* ?)*))?(?: ?:(.*))?$/', $str, $args);
			if(isset($args[1]))
				if(strrpos($args[1], '!'))
					$username = substr($args[1], 0, strrpos($args[1], '!'));
				else
					$username = $args[1];
			else
				$username = '';
				
			return array('username' => $username, 'command' => isset($args[2]) ? $args[2] : '', 'trail' => isset($args[4]) ? trim($args[4]) : '', 'args' => isset($args[3]) ? $args[3] : '');
		}

		public function loadModules($dir = null) {
			if(is_null($dir))
				$dir = dirname(__FILE__) . '\\modules';
			$modules = glob($dir . '\\*.php');
			foreach($modules as $module) {
				$module_name = basename($module, '.php');
				if(!class_exists($module_name))
					require_once($module);
				else
					echo 'Module "'.$module_name.'"" already loaded!'.PHP_EOL;
				if(class_exists($module_name)) {
					$this->IRCBOT_MODULES[$module_name] = new $module_name;
				} else
					echo 'Module "'.$dir.'\\'.$module_name.'.php" could not be loaded! Class name must match that of the filename!' . PHP_EOL;
			}
		}

		public function getModules() {
			return $this->IRCBOT_MODULES;
		}

		public function handleCommand($command) {
			if($command == 'PING')
				$this->send_cmd('PONG ' . substr($this->IRC_DATA, 5));
			if(!isset($this->EVENT_HANDLERS[$command]) || $command == 'PING') {
				$ds = $this->getResultSet();
				foreach($ds as $s)
					if(empty($s))
						$empty = true;
					else
						$empty = false;
				if(isset($empty) && $empty === false)
					echo '['.date('h:i').'] <'.trim($ds['username']).':'.$ds['command'].'> ' . trim($ds['trail']) . PHP_EOL;
				return false;
			}
			foreach($this->EVENT_HANDLERS[$command] as $func)
				$func($this);
			foreach($this->IRCBOT_MODULES as $mod) {
				if(method_exists($mod, 'EVENT_' . $command)) {
					$command = 'EVENT_'.$command;
					$mod->$command($this, $this->getResultSet());
				}
			}
			return true;
		}

		public function handleModule($mod) {
			if(isset($this->IRCBOT_MODULES[$mod])) {
				$mod = $this->IRCBOT_MODULES[$mod];
				$methods = array('pre_execute', 'execute', 'post_execute');
				foreach($methods as $method)
					if(method_exists($mod, $method))
						$mod->$method($this);
				return true;
			} else
				return false;
		}

		public function addHandler($command, closure $func) {
			if(!isset($this->EVENT_HANDLERS[$command]))
				$this->EVENT_HANDLERS[$command] = array();
			$this->EVENT_HANDLERS[$command][] = $func;

			return key(end($this->EVENT_HANDLERS));
		}

		public function removeHandler($command, $id) {
			if(!isset($this->EVENT_HANDLERS[$command][$id]))
				return false;
			unset($this->EVENT_HANDLERS[$command][$id]);

			return true;
		}

		public function setArg($arg, $value) {
			$this->IRC_ARGS[$arg] = $value;
			return $this->IRC_ARGS[$arg];
		}

		public function getArg($arg) {
			if(isset($this->IRC_ARGS[$arg]))
				return $this->IRC_ARGS[$arg];
			return null;
		}
	}
?>