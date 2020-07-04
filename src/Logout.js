import React from 'react';

import {unauthenticate} from './Auth';

export default class Logout extends React.Component {
    constructor(props) {
        super(props);
    }

    componentDidMount() {
        unauthenticate();

        this.props.history.push('/');
    }

    render() {return '';}
}