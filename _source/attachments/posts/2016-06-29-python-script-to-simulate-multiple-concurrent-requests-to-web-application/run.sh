#!/bin/bash

for i in {1..100}
do
    echo "Requests ($i) begin"
    ./http_requests.py &
done
