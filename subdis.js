var
    config = require('./conf/config'),
    io = require('socket.io').listen(config.subdis.port),
    redis = require('redis');

io.enable('browser client minification');
io.enable('browser client etag');
io.enable('browser client gzip');
io.set('log level', 1);

io.sockets.on('connection', function (socket) {
    var sub = redis.createClient(config.redis.nodes.getRandom());

    socket.on('subscribe',function(channels) {
        if (!channels) return;
        channels.forEach(function(channel) {
            sub.subscribe(channel);
            sub.on('message',function(channel,data) {
                socket.emit(channel,data);
            });
        });
    });
});
