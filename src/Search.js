import React from 'react';

export default class Grades extends React.Component {
    render() {
        let authed = this.props.authenticated;

        if (authed === true) {
            return (
                <p>this is search view</p>
            );
        } else {
            return <p>search, you aint authenticated</p>;
        }
    }
}