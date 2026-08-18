#!/usr/bin/env bash
set -e

# Determine if the port config key exists, if not, create it
function fix-config() {
    local CONFIG="$HOME/.config/devsuite/config.json"

    if [[ -f $CONFIG ]]
    then
        local PORT=$(jq -r ".port" "$CONFIG")

        if [[ "$PORT" = "null" ]]
        then
            echo "Fixing devsuite config file..."
            CONTENTS=$(jq '. + {port: "80"}' "$CONFIG")
            echo -n $CONTENTS >| "$CONFIG"
        fi
    fi
}

fix-config
