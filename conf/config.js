Array.prototype.getRandom = function() {
  return this[Math.random()*this.length|0];
}

exports.pubdis = {
  nodes: ['127.0.0.1:8000'],
  port: 8000
};
exports.subdis = {
  port: 8001
};
exports.redis = {
  nodes: ['/tmp/redis.sock']
};
