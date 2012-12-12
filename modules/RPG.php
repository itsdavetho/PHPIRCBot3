<?php
	class RPG {
		protected $user;

		public function __construct() {
			if(!is_dir('modules/RPG')) {
				mkdir('modules/RPG');
				mkdir('modules/RPG/users');
			}
		}

		public function pre_execute(IRCBot $irc_bot) {
			$this->user = $irc_bot->getResultSet()['username'];
			if(!isset($_SESSION[$this->user]) && !is_file('modules/RPG/users/' . $this->user . '.txt')) {
				$_SESSION[$this->user] = array();
				$defaults = array(
					'username' => $this->USER,
					'current_hitpoints' => 10,
					'stats' => array(
						'hitpoints' => 10,
						'strength' => 3,
						'attack' => 3,
						'defense' => 3
					),
					'enemy' => array()
				);

				$_SESSION[$this->user] = $defaults;
			} else if(is_file('modules/RPG/users/' . $this->user . '.txt')) {
				$_SESSION[$this->user] = parse_ini_file('modules/RPG/users/' . $this->user . '.txt', true);
			}
		}

		public function execute(IRCBot $irc_bot) {

		}

		public function post_execute(IRCBot $irc_bot) {
			$ini_file = $this->build_ini_file($_SESSION[$this->user], 'modules/RPG/users/' . $this->user . '.txt');
		}

		public function build_ini_file($array, $filename = false) {
			$ini = '';
			foreach($array as $key => $value) {
				if(is_array($value)) {
					$ini .= '['.trim($key).']' . PHP_EOL;
					foreach($value as $key_var => $val_var)
						$ini .= trim($key_var) . ' = ' . trim($val_var) . PHP_EOL;
					$ini .= PHP_EOL;
				} else {
					if(is_int($value))
						$value = $value;
					else if(is_string($value))
						$value = '"'.$value.'"';
					$ini .= trim($key) . ' = ' . $value . PHP_EOL;
				}
			}
			if($filename)
				file_put_contents($filename, $ini);
			return $ini;
		}
	}
?>