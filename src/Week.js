import React from 'react';

export default class Week extends React.Component {
    render() {
        let authed = this.props.authenticated;

        if (authed === true) {
            return (
                <div>
                    <div className = 'heading'>Week</div>
                    <div className = 'section'>
                        <p>week data goes here</p>
                    </div>
                </div>
            );
        } else {
            return <p>week, you aint authenticated</p>;
        }
    }
}