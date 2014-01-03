<?php
require_once("SocketServer.class.php"); // Include the File
require_once("RealmServer/Realm/parser.php"); //Include the packet parser file
$server = new SocketServer("0.0.0.0",$Config->realm_port); // Create a Server binding to the given ip address and listen to $realm_port for connections
$server->max_clients = 10; // Allow no more than 10 people to connect at a time
$server->hook("CONNECT","handle_connect"); // Run handle_connect every time someone connects
$server->hook("INPUT","handle_input"); // Run handle_input whenever text is sent to the server
$server->infinite_loop(); // Run Server Code Until Process is terminated.
$server->activate_debug = false;

function handle_connect($server,$client,$input)
{
    $client->key = RandomKey(32);

    send($client,"HC". $client->key);
    $client->state= "version";
    //SocketServer::socket_write_smart($client->socket,"HC".RandomKey(32),chr(0));
}
function handle_input($server,$client,$input)
{
    $input = str_replace(chr(10),"",$input);
    // Need to sanitize your inputs here
    $trim = trim($input); // Trim the input, Remove Line Endings and Extra Whitespace.
    
    

    //$parser::ParsePacket($client,$input);
    $buf = "";
   for ($i=0; $i < strlen($input); $i++) { 
       if ($input[$i] != chr(0)) {
           $buf .= $input[$i];

       }
       else
       {  
           $parser = new parser($client,$buf);
           $parser->start();
           $buf = "";
               
       }
      
     
   }

 
}
function send ($client,$packet)
{
  SocketServer::socket_write_smart($client->socket,$packet,chr(0));
}