import React from 'react';

export default class SpaceIndicator extends React.Component {
    render() {
        return (
            <div className = 'vertical' style = {{borderTop: '1px solid white'}}>
                <div style = {{flex: '1'}}>
                    <p>Spots</p>
                    <p>{this.props.spots}/{this.props.spots_max}</p>
                </div>
                <div>
                    <p>wAIT</p>
                    <p>{this.props.waitlist}/{this.props.waitlist_max}</p>
                </div>
            </div>
        );
    }
}