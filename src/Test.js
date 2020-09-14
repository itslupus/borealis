import React from 'react';
import {withRouter} from 'react-router-dom';

import Timetable from './components/week/Timetable.js';

class Hour extends React.Component {
    render() {
        return (
            <div className = 'hour' />
        )
    }
}

class GeneralCourse extends React.Component {
    render() {
        return (
            <div>
                <p>{this.props.name}</p>
                <div style = {{marginLeft: '1rem'}}>
                    <p>{this.props.data['meets'][0]['time']}</p>
                    <p>{this.props.data['meets'][0]['days']}</p>
                    <p>{this.props.data['meets'][0]['location']}</p>
                    <p>{this.props.data['details']['instructor']}</p>
                    {
                        (this.props.waitlisted
                            ? <p>waitlist position: {this.props.data['details']['wait_pos']}</p>
                            : '')
                    }
                </div>
                
            </div>
        )
    }
}

class Test extends React.Component {
    constructor(props) {
        super(props);

        let test_data = '{"result":{"waitlisted":{"Software Engineering 2 - COMP 4350 - A01":{"details":{"term":"Winter 2021","crn":"50181","instr":"Shaowei Wang","credit":"0.000","wait_pos":"16"},"meets":[{"type":"Lecture","time":"690:765","days":"TR","location":"TBA","length":"Jan 18, 2021 - Apr 16, 2021"}]},"Computer Graphics 2 - COMP 4490 - A01":{"details":{"term":"Winter 2021","crn":"52159","instr":"John P. Braico","credit":"0.000","wait_pos":"10"},"meets":[{"type":"Lecture","time":"630:680","days":"MWF","location":"TBA","length":"Jan 18, 2021 - Apr 16, 2021"}]}},"confirmed":{"Real-Time Systems - COMP 4550 - A01":{"details":{"term":"Winter 2021","crn":"52186","instr":"Meng C. Lau","credit":"3.000"},"meets":[{"type":"Lecture","time":"750:800","days":"MWF","location":"TBA","length":"Jan 18, 2021 - Apr 16, 2021"}]},"Computer Security - COMP 4580 - A01":{"details":{"term":"Winter 2021","crn":"50193","instr":"Noman Mohammed","credit":"3.000"},"meets":[{"type":"Lecture","time":"810:860","days":"MWF","location":"TBA","length":"Jan 18, 2021 - Apr 16, 2021"}]},"Professional Practice in Computer Science - COMP 4620 - A02":{"details":{"term":"Winter 2021","crn":"55318","instr":"Christina M. Penner","credit":"3.000"},"meets":[{"type":"Lecture","time":"780:855","days":"TR","location":"TBA","length":"Jan 18, 2021 - Apr 16, 2021"}]},"Introduction to Macroeconomic Principles - ECON 1020 - A04":{"details":{"term":"Winter 2021","crn":"59787","instr":"Umut Oguzoglu","credit":"3.000"},"meets":[{"type":"Lecture","time":"870:920","days":"MWF","location":"TBA","length":"Jan 18, 2021 - Apr 16, 2021"}]}}}}';
        test_data = JSON.parse(test_data)['result'];

        let min = Number.MAX_SAFE_INTEGER;
        let max = 0;

        Object.keys(test_data['confirmed']).forEach(key => {
            let meet_obj = test_data['confirmed'][key]['meets'][0];

            let time_obj = meet_obj['time'];
            let time_split = time_obj.split(':');

            for (let time of time_split) {
                if (time < min) min = time;
                if (time > max) max = time;
            }
        });

        this.state = {
            confirmed: test_data['confirmed'],
            waitlisted: test_data['waitlisted'],
            min: min,
            max: max
        }

        this.render_hours = this.render_hours.bind(this);
    }

    render_hours() {
        let min_hour = Math.floor(this.state.min / 60);
        let max_hour = Math.floor(this.state.max / 60);
        
        let total_hours = max_hour - min_hour + 1;

        let result = [];
        for (let i = 0; i < total_hours; i++) {
            result.push(<Hour />);
        }

        return result;
    }

    get_terms() {
        let options = [];

        let first_term = window.sessionStorage.getItem('first_term');
        let year = Number(first_term.slice(0, 4));
        let month = Number(first_term.slice(-2));

        let i = 0;
        while (i++ < 5) {
            options.push(<option>{'' + year + month}</option>);

            let tmp = month + 40;
            month = tmp % 120;
            year += Math.floor(tmp / 120);
        }

        return options;
    }

    render() {
        return (
            <div>
                <div className = 'heading vertical'>
                    <p>test page</p>
                    <select>
                        {this.get_terms()}
                    </select>
                </div>
                <div className = 'row'>
                    <div className = 'section'>
                        <b><p>week view</p></b><br />
                        <Timetable courses = {this.state.confirmed} time_bounds = {[this.state.min, this.state.max]}/>
                        {this.render_hours()}
                    </div>
                </div>
                <div className = 'row vertical'>
                    <div className = 'section'>
                        <b><p>waitlisted</p></b><br />
                        {
                            Object.keys(this.state.waitlisted).map(key => {
                                return <GeneralCourse name = {key} data = {this.state.waitlisted[key]} waitlisted = {true}/>
                            })
                        }
                    </div>
                    <div className = 'section'>
                        <b><p>registered</p></b><br />
                        {
                            Object.keys(this.state.confirmed).map(key => {
                                return <GeneralCourse name = {key} data = {this.state.confirmed[key]}/>
                            })
                        }
                    </div>
                </div>
            </div>
        );
    }
}

export default withRouter(Test);