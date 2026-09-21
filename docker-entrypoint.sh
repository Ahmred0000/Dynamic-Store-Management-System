#!/bin/sh
set -e

if [ -n "${AIVEN_CA_BASE64:-}" ]; then
    printf '%s' "$AIVEN_CA_BASE64" | base64 -d > /tmp/aiven-ca.pem
    chmod 600 /tmp/aiven-ca.pem
    export MYSQL_ATTR_SSL_CA=/tmp/aiven-ca.pem
fi

exec "$@"
