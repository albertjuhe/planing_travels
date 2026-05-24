new Autocomplete("address", {
    selectFirst: true,
    insertToInput: true,
    cache: true,
    howManyCharacters: 2,
    // onSearch
    onSearch: ({ currentValue }) => {
        // api
        const api = `https://nominatim.openstreetmap.org/search?format=geojson&limit=5&q=${encodeURI(currentValue)}`;

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

        const { display_name } = object.properties;
        const { place_id } = object.properties;
        const { osm_type, osm_id } = object.properties;
        const [lng, lat] = object.geometry.coordinates;
        $('#latPoint').val(lat);
        $('#lngPoint').val(lng);
        $('#placeId').val(place_id);

        var title = display_name.split(',')[0].trim();
        $('#title').val(title);

        if (osm_type && osm_id) {
            fetch('https://nominatim.openstreetmap.org/lookup?osm_ids=' + osm_type[0].toUpperCase() + osm_id + '&format=json&addressdetails=1&extratags=1')
                .then(function (r) { return r.json(); })
                .then(function (data) {
                    if (data && data.length > 0) {
                        if (data[0].name) {
                            $('#title').val(data[0].name);
                        }
                        if (data[0].extratags && data[0].extratags.website) {
                            $('#link').val(data[0].extratags.website);
                        }
                    }
                })
                .catch(function () {});
        }

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


