const https = require('https');

const fs = require('fs');

const options = {
  key: fs.readFileSync('/etc/pki/tls/private/star_omnisrv_com.key'),
  cert: fs.readFileSync('/etc/pki/tls/certs/star_omnisrv_com.crt')
};

var WebsocketServer = require('websocket').server;

var server = https.createServer(options, function (request, response) {

	function getPostParams(request, callback) {
		
	    var qs = require('querystring');

	    if (request.method == 'POST') {
			
	        var body = '';

	        request.on('data', function (data) {
	            
				body += data;
				
	            if (body.length > 1e6)
	                request.connection.destroy();
				
	        });

	        request.on('end', function () {
				
	            var POST = qs.parse(body);
				
	            callback(POST);
	        
			});
	    }
	}

    if (request.method === "POST") {
    	
		getPostParams(request, function(POST) {	
			
			console.log(POST.data);
			
			messageClients(POST.data);
			
			response.writeHead(200);
			
			response.end();
		
		});
		
		return;
	
	}
	
}).listen(3000);


var websocketServer = new WebsocketServer({
	httpServer: server
});

websocketServer.on("request", websocketRequest);

global.clients = {};

var connectionId = 0;

function websocketRequest(request) {

	var connection = request.accept(null, request.origin); 

	connectionId++;
	
	clients[connectionId] = connection;

}

function messageClients(message) {

	for (var i in clients) {
		clients[i].sendUTF(message);
	}

}
