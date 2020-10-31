import React from 'react';

import {withRouter} from 'react-router-dom'

class Logout extends React.Component {
    componentDidMount() {
        this.props.logout_handler();
        this.props.history.push('/');
    }

    render() {return '';}
}

export default withRouter(Logout)