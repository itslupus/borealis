import React from 'react';
import {Link} from 'react-router-dom';

export default class Navigation extends React.Component {
    render() {
        return (
            <nav>
                <ul>
                    <li><Link to = '/'>HOME</Link></li>
                    <li><Link to = 'week'>WEEK</Link></li>
                    <li><Link to = 'grades'>GRADES</Link></li>
                    <li><Link to = 'search'>SEARCH</Link></li>
                    <li><Link to = 'login'>[LOGIN]</Link></li>
                </ul>
            </nav>
        );
    }
}