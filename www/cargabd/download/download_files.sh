#!/bin/bash
#
# Baixa todos os ZIPs de uma competência dos dados abertos do CNPJ da Receita Federal.
#
# Uso:
#   ./download_files.sh AAAA-MM [pasta_destino]
#
# Exemplo:
#   ./download_files.sh 2026-10
#   ./download_files.sh 2026-10 /var/opt/dados_receita/download
#
# Sem pasta de destino, os arquivos vão para a pasta deste script.
# Downloads interrompidos continuam de onde pararam (wget -c).

COMPETENCIA=$1
DEST_DIR=${2:-$(cd "$(dirname "$0")" && pwd)}

#Cores
RED='\033[0;31m'
LGREEN='\033[0;32m'
YBLUE='\033[1;33;4;44m'
NC='\033[0m' # No Color

# Constants
BASE_URL="https://arquivos.receitafederal.gov.br/dados/cnpj/dados_abertos_cnpj"
USER_AGENT="Mozilla/5.0 (X11; Ubuntu; Linux x86_64; rv:94.0) Gecko/20100101 Firefox/94.0"

if [[ ! "$COMPETENCIA" =~ ^[0-9]{4}-[0-9]{2}$ ]]; then
    printf "${RED}Informe a competência no formato AAAA-MM. Exemplo: $0 2026-10${NC}\n" >&2
    printf "As competências disponíveis estão em ${BASE_URL}/\n" >&2
    exit 1
fi
URL="${BASE_URL}/${COMPETENCIA}/"

# Ensure destination directory exists
mkdir -p "$DEST_DIR"

# Fetch all ZIP URLs from the website
get_zip_urls() {
    local urls
    if ! urls=$(wget -qO- --user-agent="$USER_AGENT" "$URL" | grep -Eo 'href="([^"]+\.zip)"' | awk -F'"' '{print $2}'); then
        printf "${RED}Error: Failed to retrieve ZIP URLs from %s${NC}\n" "$URL" >&2
        return 1
    fi
    if [[ -z "$urls" ]]; then
        printf "${RED}Error: nenhum ZIP encontrado em %s${NC}\n" "$URL" >&2
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
        if ! wget -q -c --user-agent="$USER_AGENT" -P "$DEST_DIR" "$zip_url"; then
            printf "${RED}Error: Failed to download %s${NC}\n" "$file_name" >&2
            continue
        fi

        printf "${LGREEN}Downloaded %s successfully.${NC}\n" "$file_name"
    done <<< "$zip_urls"
}

# Main function
main() {
    printf "Baixando %s para %s\n" "$URL" "$DEST_DIR"
    if ! download_zips; then
        printf "${RED}Error: ZIP download process failed.${NC}\n" >&2
        return 1
    fi
    printf "${YBLUE} All downloads completed.${NC}\n"
}

main "$@"
