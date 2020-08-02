import React from 'react';
import {Link} from 'react-router-dom';

export default class Footer extends React.Component {
    render() {
        return (
            <footer>
                <span>not affiliated with Ellucian or the University of Manitoba</span>
                <div className = 'inline nav-links'>
                    <Link className = 'link' to = '/privacy'>privacy policy</Link>
                    <Link className = 'link' to = '/about'>about</Link>
                </div>
            </footer>
        );
    }
}