import React from 'react';
import ReactDOM from 'react-dom';
import {Route, BrowserRouter as Router} from 'react-router-dom';

import './styles.css';

import Home from './Home';
import Week from './Week';
import Grades from './Grades';
import Search from './Search';
import Login from './Login';
import Logout from './Logout';

import Navigation from './compenents/Navigation';

class App extends React.Component {
    constructor(props) {
        super(props);
    }
    
    render() {
        return (
            <React.StrictMode>
                <Router>
                    <Navigation />
                    <div className = 'app'>
                        <Route exact path = '/' component = {Home} />
                        <Route path = '/week' component = {Week} />
                        <Route path = '/grades' component = {Grades} />
                        <Route path = '/search' component = {Search} />
                        <Route path = '/login' component = {Login} />
                        <Route path = '/logout' component = {Logout} />
                    </div>
                </Router>
            </React.StrictMode>
        );
    }
}
ReactDOM.render(
    <App />,
    document.getElementById('root')
);
