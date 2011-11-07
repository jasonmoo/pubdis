var
  config = require('./conf/config'),
  http = require('http'),
  redis = require('redis'),
  pub = [];

config.redis.nodes.forEach(function(node) {
  pub.push(redis.createClient(node));
})

// url should match /<id>/<action>[/<subaction>/<subsubaction>...]
var url_match = new RegExp('(?:/[^/]+){2,}');

http.createServer(function (req, res) {

    // console.log(req);
    switch (req.method) {
        case 'POST':
            if (!url_match.test(req.url)) {
                console.log(req.url);
                res.writeHead(400,'Url does not conform to /<id>/<action>');
                res.end();
                return;
            }

            req.setEncoding('utf8');

            var data = '';
            req.on('data',function(chunk) {
                data += chunk;
            });

            req.on('end',function() {
                if (data.substring(0,5) !== 'data=') {
                    console.log('data error', data);
                    res.writeHead(400,'POST body must contain a single value called "data"');
                    res.end();
                    return;
                }

                // respond with Created and end connection before proceeding
                // console.log('201 success');
                res.writeHead(201);
                res.end();

                data = decodeURIComponent(data.substring(5)); // chop data= off the front
                var url = req.url.substring(1); // chop off leading slash

                pub.forEach(function(node) {
                    // console.log('publishing',node,url,data);
                    node.publish(url,data);
                });
                data = null;
            });
        break;
        default:
            res.writeHead(404);
            res.end();
            return;
    }

}).listen(config.pubdis.port);
