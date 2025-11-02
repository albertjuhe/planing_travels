import React from 'react';
import { BrowserRouter, Route } from 'react-router-dom'
import List from './List';
import TravelList from './model/TravelList';
import {baseUrl} from '../api/travelApi'
import AddTravel from "./AddTravel";

const Root = () => (
    <BrowserRouter>
        <div>
            <Route exact path="/public/index.php/:lang/private/new" component={AddTravel}/>
            <Route exact path="/:lang/private/new" component={AddTravel}/>
            <Route exact path="/public/index.php" component={List}/>
            <Route exact path="/public/index.php/:lang/private" component={TravelList}/>
            <Route exact path="/" component={List}/>
            <Route exact path="/:lang/private" component={TravelList}/>
        </div>
    </BrowserRouter>
);

export default Root;