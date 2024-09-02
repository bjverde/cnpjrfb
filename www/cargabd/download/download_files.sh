#!/bin/bash

ORIGEM=$1
DESTINO=$2

#Cores
RED='\033[0;31m'
LGREEN='\033[0;32m'
YBLUE='\033[1;33;4;44m'
NC='\033[0m' # No Color

# Constants
URL="https://dadosabertos.rfb.gov.br/CNPJ/dados_abertos_cnpj/2024-08/"
DEST_DIR="/var/www/html/cargabd/download" # Replace with your destination directory
USER_AGENT="Mozilla/5.0 (X11; Ubuntu; Linux x86_64; rv:94.0) Gecko/20100101 Firefox/94.0"

# Ensure destination directory exists
mkdir -p "$DEST_DIR"

# Fetch all ZIP URLs from the website
get_zip_urls() {
    local urls
    if ! urls=$(wget -qO- --user-agent="$USER_AGENT" "$URL" | grep -Eo 'href="([^"]+\.zip)"' | awk -F'"' '{print $2}'); then
        printf "${RED}Error: Failed to retrieve ZIP URLs.${NC}\n" >&2
        return 1
    fi

    # Prepend the base URL to each relative URL
    urls=$(printf "%s\n" "$urls" | sed "s|^|$URL|")
    printf "%s\n" "$urls"
}

# Download all ZIP files
download_zips() {
    local zip_urls
    if ! zip_urls=$(get_zip_urls); then
        return 1
    fi

    while IFS= read -r zip_url; do
        local file_name; file_name=$(basename "$zip_url")

        printf "Downloading %s...\n" "$file_name"
        if ! wget -q --user-agent="$USER_AGENT" -P "$DEST_DIR" "$zip_url"; then
            printf "${RED}Error: Failed to download %s${NC}\n" "$file_name" >&2
            continue
        fi

        printf "Downloaded %s successfully.\n" "$file_name"
    done <<< "$zip_urls"
}

# Main function
main() {
    if ! download_zips; then
        printf "${RED}Error: ZIP download process failed.${NC}\n" >&2
        return 1
    fi
    printf "${YBLUE} All downloads completed.${NC}\n"
}

main "$@"