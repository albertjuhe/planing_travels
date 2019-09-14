import React from 'react';
import { BrowserRouter, Route } from 'react-router-dom'
import List from './List';
import TravelList from './model/TravelList';

const Root = () => (
    <BrowserRouter>
        <div>
            <Route exact path="/planing_travels/public/index.php" component={List}/>
            <Route exact path="/planing_travels/public/index.php/en/private" component={TravelList}/>
        </div>
    </BrowserRouter>
);

export default Root;