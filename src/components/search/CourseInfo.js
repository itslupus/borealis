import React from 'react';

import DayOfWeek from './DayOfWeek';
import SpaceIndicator from './SpaceIndicator';

export default class CourseInfo extends React.Component{
    render() {
        let location = this.props.data.meets[0].location;
        if (location.includes('Distance'))
            location = 'Distance/Online';

        let spots = this.props.data.cnt;
        let spots_max = this.props.data.cap;

        let wait = this.props.data.wl_cnt;
        let wait_max = this.props.data.wl_cap;


        return (
            <div className = 'vertical' style = {{border: '1px solid white'}}>
                <div style = {{flex: '2'}}>
                    <p>{this.props.section} ({this.props.data.crn})</p>
                    <p>{this.props.data.instr}</p>
                    <p>{this.props.data.desc}</p>
                </div>
                <div style = {{textAlign: 'center', borderLeft: '1px solid white'}}>
                    <p>{location}</p>
                    <p>{this.props.data.meets[0].time}</p>
                    {location[0] !== 'D' && <DayOfWeek days = {this.props.data.meets[0].days} />}
                    <SpaceIndicator spots = {spots} spots_max = {spots_max} waitlist = {wait} waitlist_max = {wait_max} />
                </div>
            </div>
        );
    }
}