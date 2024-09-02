#!/bin/bash

ORIGEM=$1
DESTINO=$2

#Cores
RED='\033[0;31m'
LGREEN='\033[0;32m'
YBLUE='\033[1;33;4;44m'
NC='\033[0m' # No Color

# Constants
SRC_DIR="." # Replace with your source directory containing ZIP files
DEST_DIR="../extracted" # Replace with your destination directory for extracted files

# Ensure destination directory exists
# Ensure destination directory exists
mkdir -p "$DEST_DIR"

# Extract all ZIP files
extract_zips() {
    local zip_file
    for zip_file in "$SRC_DIR"/*.zip; do
        # Check if any ZIP files are found
        if [[ ! -f "$zip_file" ]]; then
            printf "No ZIP files found in %s.\n" "$SRC_DIR" >&2
            return 1
        fi

        printf "Extracting %s...\n" "$(basename "$zip_file")"
        if ! unzip -q "$zip_file" -d "$DEST_DIR"; then
            printf "${RED}Error: Failed to extract %s${NC}\n" "$(basename "$zip_file")" >&2
            continue
        fi

        printf "${LGREEN}Extracted %s successfully.${NC}\n" "$(basename "$zip_file")"
    done
}

# Main function
main() {
    if ! extract_zips; then
        printf "Error: ZIP extraction process failed.\n" >&2
        return 1
    fi
    printf "${YBLUE}All extractions completed.${NC}\n"
}

main "$@"