import React from 'react';

import {is_authenticated} from './Auth';

export default class Home extends React.Component {
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
            let cookie = document.cookie;

            return (
                <div><p>home '{cookie}'</p><p>is authed: {authed.valueOf()}</p></div>
            );
        } else {
            return <p>home, you aint authenticated</p>;
        }
    }
}