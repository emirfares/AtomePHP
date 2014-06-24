<?php
	/*!	@class		SocketServerClient
		@author		Navarr Barnier
		@abstract	A Client Instance for use with SocketServer
	 */
	class SocketServerClient
	{
		/*!	@var		socket
			@abstract	resource - The client's socket resource, for sending and receiving data with.
		 */
		protected $socket;

		/*!	@var		ip
			@abstract	string - The client's IP address, as seen by the server.
		 */
		protected $ip;

		/*!	@var		hostname
			@abstract	string - The client's hostname, as seen by the server.
			@discussion	This variable is only set after calling lookup_hostname, as hostname lookups can take up a decent amount of time.
			@see		lookup_hostname
		 */
		protected $hostname;

		/*!	@var		server_clients_index
			@abstract	int - The index of this client in the SocketServer's client array.
		 */
		protected $server_clients_index;

		/*!	@var		state
			@abstract	string - The client's packet state.
		 */
		public $state;

		/*!	@var		infos
			@abstract	array - The client's account informations.
		 */
		public $Infos;

		/*!	@var		key
			@abstract	string - The client's encryption key.
		 */
		public $key;

		/*!	@function	__construct
			@param		resource- The resource of the socket the client is connecting by, generally the master socket.
			@param		int	- The Index in the Server's client array.
			@result		void
		 */
		public function __construct(&$socket,$i)
		{
			$this->server_clients_index = $i;
			$this->socket = socket_accept($socket) OR die('Failed to accept\n');
			SocketServer::debug('New client connected\n');
			socket_getpeername($this->socket, $ip);
			$this->ip = $ip;
		}
		
		/*!	@function	lookup_hostname
			@abstract	Searches for the user's hostname and stores the result to hostname.
			@see		hostname
			@param		void
			@result		string	- The hostname on success or the IP address on failure.
		 */
		public function lookup_hostname()
		{
			$this->hostname = gethostbyaddr($this->ip);
			return $this->hostname;
		}

		public function getip()
		{
			return $this->ip;
		}	
		
		/*!	@function	destroy
			@abstract	Closes the socket.  Thats pretty much it.
			@param		void
			@result		void
		 */
		public function destroy()
		{
			socket_close($this->socket);
		}

		function &__get($name)
		{
			return $this->{$name};
		}
		
		function __isset($name)
		{
			return isset($this->{$name});
		}
	}