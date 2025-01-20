from flask import Flask, request, jsonify
from http import HTTPStatus
import time

app = Flask(__name__)

@app.route('/orders', methods=['POST'])
def slow_orders():
    time.sleep(2)
    data = request.json
    return jsonify({
        'status': 'ok',
        'message': 'Received subscription order',
        'received': data
    }), HTTPStatus.OK

if __name__ == '__main__':
    app.run(host='0.0.0.0', port=5001)
