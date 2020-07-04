import React from 'react';
import {Link} from 'react-router-dom';

export default class Navigation extends React.Component {
    constructor(props) {
        super(props);
    }

    render() {
        return (
            <nav>
                <ul>
                    <li><Link to = '/'>HOME</Link></li>
                    <li><Link to = 'week'>WEEK</Link></li>
                    <li><Link to = 'grades'>GRADES</Link></li>
                    <li><Link to = 'search'>SEARCH</Link></li>
                    <li><Link to = 'login'>[LOGIN]</Link></li>
                    <li><Link to = 'logout'>[LOGOUT]</Link></li>
                </ul>
            </nav>
        );
    }
}