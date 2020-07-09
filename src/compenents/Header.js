import React from 'react';
import {Link} from 'react-router-dom';

import {is_authenticated} from '../Auth';

export default class Header extends React.Component {
    componentDidUpdate(prev_props) {
        if (this.props.location.key !== prev_props.location.key) {
            console.log('update');
        }
    }

    componentDidMount() {
        console.log('mount');
    }

    render() {
        if (is_authenticated === true) {

        }

        return (
            <header>
                <div className = 'inline brand'>
                    <span id = 'brand'>bOrEaLiS</span>
                </div>
                <div className = 'inline nav-links'>
                    <Link className = 'link' to = '/'>HOME</Link>
                    <Link className = 'link' to = 'week'>WEEK</Link>
                    <Link className = 'link' to = 'grades'>GRADES</Link>
                    <Link className = 'link' to = 'search'>SEARCH</Link>
                    <Link className = 'link' to = 'login'>[LOGIN]</Link>
                    <Link className = 'link' to = 'logout'>[LOGOUT]</Link>
                    <Link className = 'link' to = 'test'>[TEST]</Link>
                </div>
            </header>
        );
    }
}