import React, {Component} from 'react';
import Loading from "./Loading";
import Travel from "./model/Travel";
import TravelAPI, {getBestTravels} from "../api/travelApi"

class List extends Component {

    constructor(props) {
        super(props);
        this.state = {
            isLoading: false,
            travels: null
        }
    }

    componentDidMount() {
        this.setState({isLoading: true}); //Nomes es pot fer amb set statet, temes concurrencies, si modifiques estat el component es renderitza
        getBestTravels(10)
            .then(
                json => this.setState(
                    {isLoading:false,travels: json.data}
                    )
            );
    }

    render()
    {
        const {travels, isLoading} = this.state; //El posem con a constant
        if (isLoading) { //es podria fer com this.state.isLoading
            return (<Loading message="Cargando desde List..."/>);
        }

        return (
            <React.Fragment>
            <table className="table table-striped">
                <thead>
                <tr>
                    <th>Destination</th>
                    <th>Dates</th>
                    <th>Valoration</th>
                    <th>Visualizations</th>
                    <th>Forked</th>
                    <th>Traveler</th>
                </tr>
                </thead>
                <tbody>
                {
                    travels && travels.map((travel,i) => {
                        return (<Travel key={i} data={travel} />)
                    })
                }
                </tbody>
            </table>
            </React.Fragment>
        )
    }
}

export default List;