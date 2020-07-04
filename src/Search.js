import React from 'react';

import {is_authenticated} from './Auth';

export default class Grades extends React.Component {
    constructor(props) {
        super(props);

        this.state = {
            authenticated: false
        }
    }

    componentDidUpdate(prev_props) {
        if (this.props.location.key !== prev_props.location.key) {
            this.setState({authenticated: is_authenticated()});
        }
    }

    componentDidMount() {
        this.setState({authenticated: is_authenticated()});
    }

    render() {
        let authed = this.state.authenticated;

        if (authed === true) {
            return (
                <p>this is search view</p>
            );
        } else {
            return <p>search, you aint authenticated</p>;
        }
    }
}