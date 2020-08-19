import React from 'react';

export default class Day extends React.Component {
    constructor(props) {
        super(props);

        let tmp = this.props.data;

        Object.keys(tmp).forEach(course => {
            let meet = tmp[course];
            if (!meet.offset) {
                let table_hour = Math.floor(this.props.min / 60);
    
                let course_split = meet['time'].split(':');
                let course_min_hour = course_split[0] / 60;
                let course_min_minute = course_split[0] % 60;
                let course_max_hour = course_split[1] / 60;
                let course_max_minute = course_split[1] % 60;
                
                meet.offset = (course_min_hour - table_hour) * 5 + 'rem';
                meet.height = (course_max_hour - course_min_hour) * 5 + 'rem';
                meet.time = Math.floor(course_min_hour) + ':' + course_min_minute + ' - ' + Math.floor(course_max_hour) + ':' + course_max_minute;
            }
        });

        this.state = {
            data: tmp
        };
    }

    render() {
        return (
            <div className = 'day'>
                <div className = 'day-heading'>{this.props.day}</div>
                <div className = 'day-container'>
                    {
                        Object.keys(this.state.data).map(course => {
                            return (
                                <div className = 'class' style = {{top: this.state.data[course]['offset'], height: this.state.data[course]['height']}}>
                                    {course.split(' - ')[1]}<br />
                                    {this.state.data[course]['time']}<br />
                                    {this.state.data[course]['location']}
                                </div>
                            )
                        })
                    }
                </div>
            </div>
        );
    }
}