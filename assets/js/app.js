import React from 'react';
import ReactDOM from 'react-dom';
import Root from './components/Root';

class App extends React.Component {
    render() {
        return (
            <Root/>
        )
    }
}
ReactDOM.render(<App/>, document.getElementById('root'));

