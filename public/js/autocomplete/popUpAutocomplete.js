new Autocomplete("address", {
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

        const { display_name } = object.properties;
        const { place_id } = object.properties;
        const [lng, lat] = object.geometry.coordinates;
        $('#latPoint').val(lat);
        $('#lngPoint').val(lng);
        $('#title').val(display_name);
        $('#placeId').val(place_id);


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


