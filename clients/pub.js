exports.publish = (function() {

    var http = require('http'),
        pubdis_nodes = ['127.0.0.1:8000'];

    pubdis_nodes.get_random = function() {
        return this[Math.random()*this.length|0];
    }

    return function(id, action, data) {
        var node = pubdis_nodes.get_random().split(':'),
            domain = node[0],
            port = node[1] || 80,
            path = '/'+id+'/'+action;

        var post = http.request({
            host: domain,
            port: port,
            path: path,
            method: 'POST'
        });
        post.on('error',function(e) { console.error("Problem publishing to Pubdis! "+e); });
        post.end('data='+encodeURIComponent(JSON.stringify(data)),'utf8');
    };

})();