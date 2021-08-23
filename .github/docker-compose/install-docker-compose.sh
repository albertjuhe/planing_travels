#!/usr/bin/env bash

set -xEeuo pipefail # https://vaneyckt.io/posts/safer_bash_scripts_with_set_euxo_pipefail/

curl -L https://github.com/docker/compose/releases/download/${DOCKER_COMPOSE_VERSION}/docker-compose-$(uname -s)-$(uname -m) > docker-compose
chmod +x docker-compose
sudo mv docker-compose /usr/local/bin
