#!/usr/bin/env bash

set -e

SOURCE="${BASH_SOURCE[0]}"
SUDOCMDS="uninstall install start restart stop unsecure secure use"
HOMEPATH=$HOME

ROOTPATH=$DOCROOT

ISSUDO = $SUDO




if [ ! -z "$ISSUDO" ]
    exit
fi

function check_dependencies() {
    local RED='\033[1;31m'
    local NC='\033[0m'
    local msg=''

    for cmd in "jq" "xsel" "certutil"; do
        local str=''

        if ! [[ -x "$(command -v $cmd)" ]]; then
            printf -v str " - %s\n" "$cmd"
            local msg+="$str"
        fi
    done

    if [[ $msg != '' ]]; then
        printf "${RED}You have missing DevSuite dependiencies:\n"
        printf "$msg"
        exit 1
    fi
}

# If the current source is a symbolic link, we need to resolve it to an
# actual directory name. We'll use PHP to do this easier than we can
# do it in pure Bash. So, we'll call into PHP CLI here to resolve.
if [[ -L $SOURCE ]]
then
    DIR=$(php -r "echo dirname(realpath('$SOURCE'));")
else
    DIR="$( cd "$( dirname "$SOURCE" )" && pwd )"
fi



# If the command is one of the commands that requires "sudo" privileges
# then we'll proxy the incoming CLI request using sudo, which allows
# us to never require the end users to manually specify each sudo.
if [[ -n $1 && $SUDOCMDS =~ $1 ]]
then
    check_dependencies

    if [[ "$EUID" -ne 0 ]]
    then
        sudo env HOME=$HOMEPATH SUDO=YES $SOURCE "$@"
        exit 0
    fi
fi



if [[ -n $2 && "domain port" =~ $1 ]]
then
    if [[ "$EUID" -ne 0 ]]
    then
        sudo env HOME=$HOMEPATH $SOURCE "$@"
        exit 0
    fi
fi

# If the command is the "share" command we will need to resolve out any
# symbolic links for the site. Before starting Ngrok, we will fire a
# process to retrieve the live Ngrok tunnel URL in the background.
if [[ "$1" = "share" ]]
then
    check_dependencies

    HOST="${PWD##*/}"
    DOMAIN=$(cat "$HOME/.devsuite/config.json" | jq -r ".domain")
    PORT=$(cat "$HOME/.devsuite/config.json" | jq -r ".port")

    for linkname in ~/.devsuite/Sites/*; do
        if [[ "$(readlink ${linkname})" = "$PWD" ]]
        then
            HOST="${linkname##*/}"
        fi
    done

    # Decide the correct PORT to use according if the site has a secure
    # config or not.
    if grep --no-messages --quiet 443 ~/.devsuite/Nginx/$HOST*
    then
        PORT=88
    else
        PORT=80
    fi

    # Fetch Ngrok URL In Background...
    bash "$DIR/cli/scripts/fetch-share-url.sh" &
    sudo -u $USER "$DIR/bin/ngrok" http "$HOST.$DOMAIN:$PORT" -host-header=rewrite ${*:2}
    exit

# Finally, for every other command we will just proxy into the PHP tool
# and let it handle the request. These are commands which can be run
# without sudo and don't require taking over terminals like Ngrok.
else
    php "$DIR/cli/valet.php" "$@"
fi
