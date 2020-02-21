"use strict";

var http = require('http');

var url = require('url');

var WebsocketServer = require('websocket').server;

var server = http.createServer(function(request,response) {
	

	function getPostParams(request, callback) {
	    var qs = require('querystring');

	    if (request.method == 'POST') {
	        var body = '';

	        request.on('data', function (data) {
	            body += data;

	            // Too much POST data, kill the connection!
	            // 1e6 === 1 * Math.pow(10, 6) === 1 * 1000000 ~~~ 1MB
	            if (body.length > 1e6)
	                request.connection.destroy();
	        });

	        request.on('end', function () {
	            var POST = qs.parse(body);
	            callback(POST);
	        });
	    }
	}

    // in-server request from PHP
    if (request.method === "POST") {
    	
		getPostParams(request, function(POST) {	
			
			console.log(POST.data);
			
			messageClients(POST.data);
			
			response.writeHead(200);
			response.end();
		
		});
		
		return;
	
	}
	
});

server.listen(3000, function() {
    console.log('listening on *:8080');
});

/* 
	Handling websocket requests
*/

var websocketServer = new WebsocketServer({
	httpServer: server
});

websocketServer.on("request", websocketRequest);


// websockets storage
global.clients = {}; // connected clients
var connectionId = 0; // incremental unique ID for each connection (this does not decrement on close)


function websocketRequest(request) {

	// TODO: Validate Hostname in Production Mode
	// TIP: You can also get query params and do different setups 

	// start the connection
	var connection = request.accept(null, request.origin); 

	connectionId++;

	// save the connection for future reference
	clients[connectionId] = connection;

}

function messageClients(message) {

	for (var i in clients) {
		clients[i].sendUTF(message);
	}

}

