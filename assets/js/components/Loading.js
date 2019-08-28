import React, {Component} from 'react';
import PropTypes from 'prop-types';

class Loading extends Component {
    render() {
        return (<div className='loading'>{this.props.message || 'Loading travel list...'}</div> );
    }
}

Loading.prototype = {
    message: PropTypes.string.isRequired
}

export default Loading