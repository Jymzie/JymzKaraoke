var express = require('express')();
var server = require('http').Server(express);
var io = require("socket.io")(server, {
    handlePreflightRequest: (req, res) => {
        const headers = {
          "Access-Control-Allow-Headers": "Content-Type, Authorization",
          "Access-Control-Allow-Origin": req.headers.origin, //or the specific origin you want to give access to,
          "Access-Control-Allow-Credentials": true
        };
        res.writeHead(200, headers);
        res.end();
    }
});

io.on('connection', function (socket) {
    console.log("new client connected");

    socket.on('reload_data', function (item) {
      io.emit('reloadme', item)
      console.log('do '+item)
    })
    socket.on('toggleplay', function () {
      io.emit('playpause')
      console.log('do playpause')
    })
    socket.on('givetotaldur', function (time) {
      io.emit('taketotaldur',time)
    })
    socket.on('settimestamp', function (time) {
      io.emit('loadtimestamp',time)
    })
    socket.on('setdur', function (per) {
      io.emit('loaddur',per)
    })
    socket.on('reqfullscreen', function () {
      io.emit('dofullscreen')
    })
});

server.listen(9090, function () {
    console.log('socket.io server listen at 9090');
});