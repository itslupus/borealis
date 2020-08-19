import React from 'react';

import Day from './Day.js'

export default class Timetable extends React.Component {
    constructor(props) {
        super(props);

        let tmp_day_data = {
            S: {},
            M: {},
            T: {},
            W: {},
            R: {},
            F: {},
            U: {}
        }

        let data_ref = this.props.courses;
        Object.keys(data_ref).forEach(key => {
            let meet_obj = data_ref[key]['meets'][0];
            let days = meet_obj.days;

            if (days !== '') {
                days = days.split('');

                for (let day of days) {
                    tmp_day_data[day][key] = meet_obj;
                }
            }
        });

        this.state = tmp_day_data;
    }

    render() {
        return (
            <div className = 'vertical'>
                <Day day = 'saturday' data = {this.state.S} min = {this.props.time_bounds[0]}/>
                <Day day = 'monday' data = {this.state.M} min = {this.props.time_bounds[0]} />
                <Day day = 'tuesday' data = {this.state.T} min = {this.props.time_bounds[0]} />
                <Day day = 'wednesday' data = {this.state.W} min = {this.props.time_bounds[0]} />
                <Day day = 'thursday' data = {this.state.R} min = {this.props.time_bounds[0]} />
                <Day day = 'friday' data = {this.state.F} min = {this.props.time_bounds[0]} />
                <Day day = 'sunday' data = {this.state.U} min = {this.props.time_bounds[0]} />
            </div>
        );
    }
}