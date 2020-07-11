import React from 'react';

export default class Week extends React.Component {
    render() {
        let authed = this.props.authenticated;

        if (authed === true) {
            return (
                <p>this is week view</p>
            );
        } else {
            return <p>week, you aint authenticated</p>;
        }
    }
}