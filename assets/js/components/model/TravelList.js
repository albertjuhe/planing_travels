import React, {Component} from 'react'
import {getTravelsByUser} from "../../api/travelApi";
import Loading from "../Loading";
import Detail from "./Detail";

class TravelList extends Component {

    constructor(props) {
        super(props);
        this.state = {
            isLoading: false,
            travels: null,
            error: null
        }
    }

    componentDidMount() {
        this.setState({isLoading: true});
        try {
            getTravelsByUser(1).then( json => this.setState(
                {isLoading: false, travels: json.data}
            )
        );
        } catch(error) {
            this.setState({error: error, isLoading: false});
        }
    }

    render() {
        const {travels, isLoading, error} = this.state; //El posem con a constant

        if (error) {
            return(<div>Error loading travels</div>);
        }

        if (isLoading) { //es podria fer com this.state.isLoading
            return (<Loading message="Cargando desde List..."/>);
        }

        return (
            <React.Fragment>
                    {
                        travels && travels.map((travel,i) => {
                            return (<Detail key={i} data={travel} />)
                        })
                    }
            </React.Fragment>
        )
    }
}

export default TravelList;