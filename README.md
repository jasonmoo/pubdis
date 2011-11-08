# Pubdis
A simple socket.io/redis pubsub app

## Requirements:
* Node.js 0.6
* Socket.io 0.8.7
* Redis 0.6.7

## Usage:

#### In browser subscription:
Subscribe through the socket.io interface

    <script src="http:/127.0.0.1:8001/socket.io/socket.io.js"></script>
    <script>
      var socket = io.connect('http://127.0.0.1:8001');
      socket.emit('subscribe',[
        '1234/likes',
        '1234/comments'
      ]);
      socket.on('1234/likes',function(data) {
        document.body.innerText = data;
      });
      socket.on('1234/comments',function(data) {
        document.body.innerText = data;
      });
    </script>

#### Server-side publishing:
Publish through any of the client wrappers or direct http POST

##### PHP
    require('pub.php');
    Pubdis::publish('1234','likes',55);
    Pubdis::publish('1234','comments',array(
        'text' => 'dope vid bro',
        'dts' => 1320795715,
    ));

##### Python
    from pub import Pubdis
    Pubdis.publish('1234','likes',55)
    Pubdis.publish('1234','comments',{'text': 'dope vid bro','dts': 1320795715,})

##### Node.js
    var pubdis = require('./pub.js');
    pubdis.publish('1234','likes',55);
    pubdis.publish('1234','comments',{'text': 'dope vid bro','dts': 1320795715,});

##### HTTP
path is /id/action[/subaction[/subsubaction[etc]]]
data should be passed as a json encoded, url encoded string

    POST /1234/likes HTTP/1.1
    Host: 127.0.0.1:8000
    data=%7B%27text%27%3A+%27dope+vid+bro%27%2C%27dts%27%3A+1320795715%2C%7D
