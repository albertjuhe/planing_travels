import React, {Component} from 'react';
import { compose, withProps } from "recompose"
import credentials from "../credentials/credentials";
import { Map, GoogleApiWrapper } from 'google-maps-react'
// key=AIzaSyBDFGff7mQ3mWhfZ5IYuqPxsCd049nqIn4 -->

const mapStyles = {
    width: '100%',
    height: '500px'
};

export class MapContainer extends Component {
    render() {
        return (
            <Map
                google={this.props.google}
                zoom={14}
                style={mapStyles}
                initialCenter={{
                    lat: -1.2884,
                    lng: 36.8233
                }}
            />
        );
    }
}

export default GoogleApiWrapper({
    apiKey: credentials.mapsKey
})(MapContainer);

