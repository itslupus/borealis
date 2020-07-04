import React from 'react';

import {is_authenticated} from './Auth';

export default class Week extends React.Component {
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
                <p>this is week view</p>
            );
        } else {
            return <p>week, you aint authenticated</p>;
        }
    }
}