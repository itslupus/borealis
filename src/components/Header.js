import React from 'react';
import {Link} from 'react-router-dom';

export default class Header extends React.Component {
    render() {
        let brand = (
            <div className = 'inline brand'>
                <span id = 'brand'>bOrEaLiS</span>
            </div>
        );
        
        if (this.props.authenticated === true) {
            return (
                <header>
                    {brand}
                    <div className = 'inline'>
                        <Link className = 'link' to = '/'>HOME</Link>
                        <Link className = 'link' to = 'week'>WEEK</Link>
                        <Link className = 'link' to = 'grades'>GRADES</Link>
                        <Link className = 'link' to = 'search'>SEARCH</Link>
                    </div>
                    <div className = 'inline float-right'>
                        <Link className = 'link' to = 'logout'>[LOGOUT]</Link>
                        <Link className = 'link' to = 'test'>[TEST]</Link>
                    </div>
                </header>
            );
        } else {
            return (
                <header>
                    {brand}
                    <div className = 'inline nav-links'>
                        <Link className = 'link' to = 'test'>[TEST]</Link>
                    </div>
                </header>
            );
        }
    }
}