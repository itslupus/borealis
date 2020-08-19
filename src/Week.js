import React from 'react';

export default class Week extends React.Component {
    componentDidUpdate(prev_props) {
        if (this.props.location.key !== prev_props.location.key) {
            this.fetch_data();
        }
    }

    componentDidMount() {
        this.fetch_data();
    }
    
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