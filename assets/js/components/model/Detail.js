import React, { Component } from 'react';
import { baseUrl } from '../../api/travelApi';
import { connectToTravelRoom } from '../websockets';

class TravelDetail extends Component {
    constructor(props) {
        super(props);
        this.state = {
            connectedCount: 0,
            connectedUsers: [],
        };
        this.ws = null;
    }

    componentDidMount() {
        const { data } = this.props;
        this.ws = connectToTravelRoom(
            data.id,
            () => {},
            ({ count, usernames }) => {
                this.setState({ connectedCount: count, connectedUsers: usernames });
            }
        );
    }

    componentWillUnmount() {
        if (this.ws) {
            this.ws.close();
        }
    }

    render() {
        const { data } = this.props;
        const { connectedCount, connectedUsers } = this.state;

        return (
            <div className="col-sm-3">
                <div>
                    <h3><a href={`${baseUrl}en/travel/${data.slug}`}>{data.title}</a></h3>
                    <a href={`${baseUrl}/en/private/travel/${data.slug}/update`}>Edit</a>
                    <a href={`${baseUrl}en/travel/publish/${data.slug}`}>Publish</a>
                </div>
                <div>
                    <img id={`${data.id}`} className="featurette-image img-responsive"
                         data-src="holder/holder.js/200x200/auto" alt="Travel photo"/>
                </div>
                <div className="date-travel">
                    <a href="#">{data.startAt.date} to {data.endAt.date}</a>
                </div>
                <div className="date-travel">
                    <span>STATUS: {data.status}</span>
                </div>
                <div className="info-travel">
                    {data.description}
                </div>
                <div>
                    <span className="glyphicon glyphicon-star" aria-hidden="true"/> Likes {data.stars}
                    <span className="glyphicon glyphicon-eye-open" aria-hidden="true"/> Watch {data.watch}
                </div>
                <div className="connected-users">
                    <span className="glyphicon glyphicon-user" aria-hidden="true"/>
                    {' '}{connectedCount} connected
                    {connectedUsers.length > 0 && (
                        <span title={connectedUsers.join(', ')}> ({connectedUsers.join(', ')})</span>
                    )}
                </div>
            </div>
        );
    }
}

export default TravelDetail;
