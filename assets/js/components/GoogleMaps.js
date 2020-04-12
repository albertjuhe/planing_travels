import React, {Component} from 'react';
import credentials from "../credentials/credentials";
import {Map, GoogleApiWrapper, InfoWindow, Marker} from 'google-maps-react'

const mapStyles = {
    width: '100%',
    height: '500px'
};

export class MapContainer extends Component {

    constructor(props) {
        super(props)

        this.state = {
            showingInfoWindow: false,
            activeMaker: {},
            selectedPlace: {}
        };
    }

    onMarkerClick = (props, marker, e) =>
        this.setState(
            {
                selectedPlace: props,
                activeMarker: marker,
                showingInfoWindow: true
            }
        );

    onClose = props => {
        if (this.state.showingInfoWindow) {
            this.setState({
                showingInfoWindow: false,
                activeMaker: null
            });
        }
    };

    render() {
        return (
            <Map
                google={this.props.google}
                zoom={14}
                style={mapStyles}
                initialCenter={{
                    lat: 40.730610,
                    lng: -73.935242
                }}>
                <Marker
                    onClick={this.onMarkerClick}
                    name={'New York City'}/>
                <InfoWindow
                    marker={this.state.activeMarker}
                    visible={this.state.showingInfoWindow}
                    onClose={this.onClose}>
                    <div>
                        <h4>{this.state.selectedPlace.name}</h4>
                    </div>
                </InfoWindow>

            </Map>
        );
    }
}

export default GoogleApiWrapper({
    apiKey: credentials.mapsKey
})(MapContainer);

