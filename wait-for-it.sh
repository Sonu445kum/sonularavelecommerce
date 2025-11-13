#!/usr/bin/env bash
# wait-for-it.sh
# Usage: ./wait-for-it.sh host:port -- command

set -e

hostport=(${1//:/ })
host=${hostport[0]}
port=${hostport[1]}

shift

until nc -z $host $port; do
  echo "Waiting for $host:$port..."
  sleep 1
done

exec "$@"
