import React from 'react';
import { BrowserRouter, Route } from 'react-router-dom'
import List from './List';
import TravelList from './model/TravelList';
import {baseUrl} from '../api/travelApi'

const Root = () => (
    <BrowserRouter>
        <div>
            <Route exact path="/" component={List}/>
            <Route exact path="/en/private" component={TravelList}/>
        </div>
    </BrowserRouter>
);

export default Root;