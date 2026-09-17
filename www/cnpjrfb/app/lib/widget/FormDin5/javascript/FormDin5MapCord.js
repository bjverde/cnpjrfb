function fd5InitMap(idField, defaultLat, defaultLon, zoom, fieldsReadOnly, geoJsonPath) {
    const mapElementId = idField + '_map';
    const mapContainer = document.getElementById(mapElementId);
    if (!mapContainer) {
        console.error('Map container not found: ' + mapElementId);
        return;
    }

    // Evita re-inicialização caso o elemento já tenha um mapa ativo (comum em AJAX/SPAs)
    if (mapContainer._leaflet_id) {
        return;
    }

    // Referência aos campos de input
    const inputLat = document.getElementById(idField + '_lat');
    const inputLon = document.getElementById(idField + '_lon');

    // Determina a coordenada inicial prioritariamente do valor atual dos campos (se existirem)
    let initialLat = defaultLat;
    let initialLon = defaultLon;

    if (inputLat && inputLon) {
        let latVal = parseFloat(inputLat.value);
        let lonVal = parseFloat(inputLon.value);
        if (!isNaN(latVal) && !isNaN(lonVal)) {
            initialLat = latVal;
            initialLon = lonVal;
        }
    }

    // Inicializa o mapa com Leaflet
    const map = L.map(mapElementId).setView([initialLat, initialLon], zoom);

    // Adiciona a camada de tiles do OpenStreetMap
    L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
        maxZoom: 19,
        attribution: '© OpenStreetMap'
    }).addTo(map);

    // Configura explicitamente os caminhos das imagens do marcador padrão do Leaflet localmente
    // Isso evita falhas de detecção automática causadas pelo carregamento via AJAX/SPA no Adianti
    const defaultIcon = L.icon({
        iconUrl: 'app/lib/widget/FormDin5/leaflet/images/marker-icon.png',
        iconRetinaUrl: 'app/lib/widget/FormDin5/leaflet/images/marker-icon-2x.png',
        shadowUrl: 'app/lib/widget/FormDin5/leaflet/images/marker-shadow.png',
        iconSize: [25, 41],
        iconAnchor: [12, 41],
        popupAnchor: [1, -34],
        tooltipAnchor: [16, -28],
        shadowSize: [41, 41]
    });
    L.Marker.prototype.options.icon = defaultIcon;

    // Adiciona o marcador ao mapa
    const marker = L.marker([initialLat, initialLon], {
        draggable: !fieldsReadOnly
    }).addTo(map);

    // Função auxiliar para atualizar os campos de input e disparar o evento change
    function updateInputs(lat, lon) {
        if (inputLat) {
            inputLat.value = lat.toFixed(6);
            inputLat.dispatchEvent(new Event('change'));
        }
        if (inputLon) {
            inputLon.value = lon.toFixed(6);
            inputLon.dispatchEvent(new Event('change'));
        }
    }

    // Evento de arrastar o marcador (drag)
    marker.on('dragend', function(e) {
        const position = marker.getLatLng();
        updateInputs(position.lat, position.lng);
    });

    // Evento de clique no mapa para posicionar o marcador
    if (!fieldsReadOnly) {
        map.on('click', function(e) {
            const position = e.latlng;
            marker.setLatLng(position);
            updateInputs(position.lat, position.lng);
        });
    }

    // Atualização em tempo real caso o usuário digite nos inputs (caso estejam visíveis e não readOnly)
    function handleManualInputChange() {
        const latVal = parseFloat(inputLat.value);
        const lonVal = parseFloat(inputLon.value);
        if (!isNaN(latVal) && !isNaN(lonVal)) {
            const newPos = [latVal, lonVal];
            marker.setLatLng(newPos);
            map.setView(newPos);
        }
    }

    if (inputLat && inputLon && !fieldsReadOnly) {
        inputLat.addEventListener('change', handleManualInputChange);
        inputLon.addEventListener('change', handleManualInputChange);
    }

    // Carrega e plota o arquivo GeoJSON se fornecido
    if (geoJsonPath) {
        fetch(geoJsonPath)
            .then(response => {
                if (!response.ok) {
                    throw new Error("Erro de rede ao carregar o arquivo GeoJSON.");
                }
                return response.json();
            })
            .then(data => {
                L.geoJSON(data, {
                    style: function (feature) {
                        return {
                            color: "#FF5722",
                            weight: 2,
                            opacity: 1,
                            fillColor: "#FFC107",
                            fillOpacity: 0.3
                        };
                    },
                    onEachFeature: function (feature, layer) {
                        let nomeRA = feature.properties.ra || feature.properties.nome || feature.properties.NM_MUN || "Região Desconhecida";
                        layer.bindPopup("<b>Região:</b><br>" + nomeRA);
                    }
                }).addTo(map);
            })
            .catch(error => {
                console.error("Erro ao carregar o GeoJSON (" + geoJsonPath + "):", error);
            });
    }

    // Armazena a referência no container para evitar duplicações
    mapContainer._leaflet_map = map;
}
