import React from 'react';
import ReactDOM from 'react-dom';
import {Route, Link, BrowserRouter as Router} from 'react-router-dom';

import Alpha from './Alpha';
import Bravo from './Bravo';
import Charlie from './Charlie';
import Home from './Home';

ReactDOM.render(
    <React.StrictMode>
        <Router>
            <div>
                <ul>
                    <li>
                        <Link to = '/'>Home</Link>
                    </li>
                    <li>
                        <Link to = '/alpha'>Alpha</Link>
                    </li>
                    <li>
                        <Link to = '/bravo'>Bravo</Link>
                    </li>
                    <li>
                        <Link to = '/charlie'>Charlie</Link>
                    </li>
                </ul>
                <Route exact path = '/' component = {Home} />
                <Route path = '/alpha' component = {Alpha} />
                <Route path = '/bravo' component = {Bravo} />
                <Route path = '/charlie' component = {Charlie} />
            </div>
        </Router>
    </React.StrictMode>,
    document.getElementById('root')
);
