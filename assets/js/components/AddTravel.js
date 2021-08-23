import React, {Component} from 'react';
import MapContainer from './GoogleMaps'
import LocationSearchInput from './PlaceAutoComplete'

class AddTravel extends Component {

    constructor(props) {
        super(props)
        this.state = {
            address:'',
            hasError: false,
            showSending: false,
            googleMapLink: '',
            title: '',
            description: '',
            lat: 0,
            lng: 0,
        };

        this.handleChange = this.handleChange.bind(this);
        this.handleSubmit = this.handleSubmit.bind(this);
        this.handleAutoComplete = this.handleAutoComplete.bind(this);
    }

    handleChange(field) {
        return (event) => {
            this.setState(
                {
                    [field]: event.target.value
                }
            )
        }
    }

    handleAutoComplete(geolocation, address) {
        this.setState({
            lat: geolocation.lat,
            lng: geolocation.lng,
            title: address
        });
    }

    handleSubmit(e) {
        e.preventDefault();
        this.setState({showSending: true});
    }

    render() {
        const {showSending, hasError, title, description, lat, lng} = this.state //state relation
        return (
            <div className="row">
                <div className="col-sm-4">
                    {showSending && (<span className="success">Enviando...</span>)}

                       <div className="form-group">
                            <label className="control-label" htmlFor="address">Search destination</label>
                            <LocationSearchInput className="form-control" handleAutoComplete={this.handleAutoComplete}/>
                        </div>
                        <div className="form-group">
                            <label className="control-label required" htmlFor="travel_title">What's the name of your trip?</label>
                            <input type="text" value={title} id="autocomplete" onChange={this.handleChange("title")} name="travel[title]" required="required"
                                   className="form-control"/>
                        </div>
                        <div className="form-group">
                            <label className="control-label required" htmlFor="travel_description">Describe your trip:</label>
                            <textarea id="travel_description" onChange={this.handleChange("description")}
                                      value={description} name="travel[description]" required="required"
                                      className="form-control"/>
                        </div>
                        <div className="form-group">
                            <div className="control-label">Do you know the dates for your trip?</div>
                            <label className="control-label required">Start date</label>
                            <div id="travel_startAt" className="form-inline">
                                <div className="sr-only">
                                    <label className="control-label required" htmlFor="travel_startAt_year">Year</label>
                                    <label className="control-label required"
                                           htmlFor="travel_startAt_month">Month</label>
                                    <label className="control-label required" htmlFor="travel_startAt_day">Day</label>
                                </div>
                                <select id="travel_startAt_year" name="travel[startAt][year]" className="form-control">
                                    <option value="2015">2015</option>
                                    <option value="2016">2016</option>
                                    <option value="2017">2017</option>
                                    <option value="2018">2018</option>
                                    <option value="2019">2019</option>
                                    <option value="2020">2020</option>
                                    <option value="2021">2021</option>
                                    <option value="2022">2022</option>
                                    <option value="2023">2023</option>
                                    <option value="2024">2024</option>
                                    <option value="2025">2025</option>
                                </select>
                                <select id="travel_startAt_month" name="travel[startAt][month]"
                                        className="form-control">
                                    <option value="1">Jan</option>
                                    <option value="2">Feb</option>
                                    <option value="3">Mar</option>
                                    <option value="4">Apr</option>
                                    <option value="5">May</option>
                                    <option value="6">Jun</option>
                                    <option value="7">Jul</option>
                                    <option value="8">Aug</option>
                                    <option value="9">Sep</option>
                                    <option value="10">Oct</option>
                                    <option value="11">Nov</option>
                                    <option value="12">Dec</option>
                                </select>
                                <select id="travel_startAt_day" name="travel[startAt][day]" className="form-control">
                                    <option value="1">1</option>
                                    <option value="2">2</option>
                                    <option value="3">3</option>
                                    <option value="4">4</option>
                                    <option value="5">5</option>
                                    <option value="6">6</option>
                                    <option value="7">7</option>
                                    <option value="8">8</option>
                                    <option value="9">9</option>
                                    <option value="10">10</option>
                                    <option value="11">11</option>
                                    <option value="12">12</option>
                                    <option value="13">13</option>
                                    <option value="14">14</option>
                                    <option value="15">15</option>
                                    <option value="16">16</option>
                                    <option value="17">17</option>
                                    <option value="18">18</option>
                                    <option value="19">19</option>
                                    <option value="20">20</option>
                                    <option value="21">21</option>
                                    <option value="22">22</option>
                                    <option value="23">23</option>
                                    <option value="24">24</option>
                                    <option value="25">25</option>
                                    <option value="26">26</option>
                                    <option value="27">27</option>
                                    <option value="28">28</option>
                                    <option value="29">29</option>
                                    <option value="30">30</option>
                                    <option value="31">31</option>
                                </select>
                            </div>
                        </div>
                        <div className="form-group">
                            <label className="control-label required">End date</label>
                            <div id="travel_endAt" className="form-inline">
                                <div className="sr-only">
                                    <label className="control-label required" htmlFor="travel_endAt_year">Year</label>
                                    <label className="control-label required" htmlFor="travel_endAt_month">Month</label>
                                    <label className="control-label required" htmlFor="travel_endAt_day">Day</label>
                                </div>
                                <select id="travel_endAt_year" name="travel[endAt][year]" className="form-control">
                                    <option value="2015">2015</option>
                                    <option value="2016">2016</option>
                                    <option value="2017">2017</option>
                                    <option value="2018">2018</option>
                                    <option value="2019">2019</option>
                                    <option value="2020">2020</option>
                                    <option value="2021">2021</option>
                                    <option value="2022">2022</option>
                                    <option value="2023">2023</option>
                                    <option value="2024">2024</option>
                                    <option value="2025">2025</option>
                                </select>
                                <select id="travel_endAt_month" name="travel[endAt][month]"
                                        className="form-control">
                                    <option value="1">Jan</option>
                                    <option value="2">Feb</option>
                                    <option value="3">Mar</option>
                                    <option value="4">Apr</option>
                                    <option value="5">May</option>
                                    <option value="6">Jun</option>
                                    <option value="7">Jul</option>
                                    <option value="8">Aug</option>
                                    <option value="9">Sep</option>
                                    <option value="10">Oct</option>
                                    <option value="11">Nov</option>
                                    <option value="12">Dec</option>
                                </select>
                                <select id="travel_endAt_day" name="travel[endAt][day]" className="form-control">
                                    <option value="1">1</option>
                                    <option value="2">2</option>
                                    <option value="3">3</option>
                                    <option value="4">4</option>
                                    <option value="5">5</option>
                                    <option value="6">6</option>
                                    <option value="7">7</option>
                                    <option value="8">8</option>
                                    <option value="9">9</option>
                                    <option value="10">10</option>
                                    <option value="11">11</option>
                                    <option value="12">12</option>
                                    <option value="13">13</option>
                                    <option value="14">14</option>
                                    <option value="15">15</option>
                                    <option value="16">16</option>
                                    <option value="17">17</option>
                                    <option value="18">18</option>
                                    <option value="19">19</option>
                                    <option value="20">20</option>
                                    <option value="21">21</option>
                                    <option value="22">22</option>
                                    <option value="23">23</option>
                                    <option value="24">24</option>
                                    <option value="25">25</option>
                                    <option value="26">26</option>
                                    <option value="27">27</option>
                                    <option value="28">28</option>
                                    <option value="29">29</option>
                                    <option value="30">30</option>
                                    <option value="31">31</option>
                                </select>
                            </div>
                        </div>
                        <div className="form-group">
                            <label className="control-label required" htmlFor="travel_geoLocation_lat">Lat</label>
                            <input type="number" id="travel_geoLocation_lat" name="travel[geoLocation][lat]"
                                   value={lat} onChange={this.handleChange("lat")}
                                   required="required" className="form-control"/>
                        </div>
                        <div className="form-group">
                            <label className="control-label required" htmlFor="travel_geoLocation_lng">Lng</label>
                            <input type="number" id="travel_geoLocation_lng" name="travel[geoLocation][lng]"
                                   value={lng} onChange={this.handleChange("lng")}
                                   required="required" className="form-control"/>
                        </div>
                        <input type="submit" className="btn btn-primary" value="Save" disabled={showSending}/>


                </div>

            </div>

        );
    }
}

export default AddTravel