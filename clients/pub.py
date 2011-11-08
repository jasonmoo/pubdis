import httplib, urllib, random

import logging

try:
    import json
except ImportError:
    raise ImportError, "Can't load a json library"

class Pubdis:
    pubdis_nodes = ['127.0.0.1:8000']

    @staticmethod
    def publish(id, action, data):

        domain = random.choice(Pubdis.pubdis_nodes)
        path = '/'+id+'/'+action
        params = urllib.urlencode({'data': json.dumps(data) })

        conn = httplib.HTTPConnection(domain)
        conn.request("POST", path, params)

        conn.sock.settimeout(0.5)

        response = conn.getresponse()

        data = response.read()
        conn.close()
