new Autocomplete("search", {
    selectFirst: true,
    insertToInput: true,
    cache: true,
    howManyCharacters: 2,
    // onSearch
    onSearch: ({ currentValue }) => {
        // api
        const api = `https://nominatim.openstreetmap.org/search?format=geojson&limit=5&city=${encodeURI(currentValue)}`;

        return new Promise((resolve) => {
            fetch(api)
                .then((response) => response.json())
                .then((data) => {
                    resolve(data.features);
                })
                .catch((error) => {
                    console.error(error);
                });
        });
    },

    // nominatim GeoJSON format
    onResults: ({ currentValue, matches, template }) => {
        const regex = new RegExp(currentValue, "gi");

        // if the result returns 0 we
        // show the no results element
        return matches === 0
            ? template
            : matches
                .map((element) => {
                    return `
                <li>
                  <p>
                    ${element.properties.display_name.replace(
                        regex,
                        (str) => `<b>${str}</b>`
                    )}
                  </p>
                </li> `;
                })
                .join("");
    },

    onSubmit: ({ object }) => {
        // remove all layers from the map
        map.eachLayer(function (layer) {
            if (!!layer.toGeoJSON) {
                map.removeLayer(layer);
            }
        });

        const { display_name } = object.properties;
        const [lat, lng] = object.geometry.coordinates;
        // custom id for marker

        const marker = L.marker([lng, lat], {
            title: display_name,
        });

        $('#travel_geoLocation_lng').val(lat);
        $('#travel_geoLocation_lat').val(lng);

        marker.addTo(map).bindPopup(display_name);

        map.setView([lng, lat], 8);
    },

    // get index and data from li element after
    // hovering over li with the mouse or using
    // arrow keys ↓ | ↑
    onSelectedItem: ({ index, element, object }) => {
        console.log("onSelectedItem:", { index, element, object });
    },

    // the method presents no results
    // no results
    noResults: ({ currentValue, template }) =>
        template(`<li>No results found: "${currentValue}"</li>`),
});

// --------------------------------------------------

// MAP PART
const config = {
    minZoom: 4,
    maxZomm: 18,
};
// magnification with which the map will start
const zoom = 3;
// coordinates
const lat = 52.22977;
const lng = 21.01178;

// calling map
const map = L.map("map", config).setView([lat, lng], zoom);

L.tileLayer('http://{s}.google.com/vt/lyrs=m&x={x}&y={y}&z={z}',{
    maxZoom: 20,
    subdomains:['mt0','mt1','mt2','mt3']
}).addTo(map);

