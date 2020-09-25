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
class Week extends React.Component {
    constructor(props) {
        super(props);

        this.state = {
            term: window.sessionStorage.getItem('last_term'),
            state: 0
        };

        this.fetch_data = this.fetch_data.bind(this);
        this.render_hours = this.render_hours.bind(this);
        this.get_terms = this.get_terms.bind(this);
    }

    componentDidUpdate(prev_props) {
        if (this.props.location.key !== prev_props.location.key) {
            this.fetch_data();
        }
    }

    componentDidMount() {
        this.fetch_data();
    }

    fetch_data() {
        if (this.props.authenticated === true) {
            this.setState({state: 0});

            let post_data = new URLSearchParams();
            post_data.append('term', this.state.term);
    
            fetch('api/FetchRegisteredCourses.php', {
                method: 'POST',
                body: post_data
            })
            .then(response => {
                // not 200 OK
                if (response.ok === false)
                    throw new Error('I told ya don\'t touch that darn thing.')
    
                return response.json();
            })
            .then(
                (data) => {
                    let result = data.result;
                    let min = Number.MAX_SAFE_INTEGER;
                    let max = 0;

                    Object.keys(result.confirmed).forEach(key => {
                        let meet_obj = result.confirmed[key]['meets'][0];

                        let time_obj = meet_obj['time'];
                        let time_split = time_obj.split(':');

                        for (let time of time_split) {
                            if (time < min) min = time;
                            if (time > max) max = time;
                        }
                    });

                    this.setState({
                        confirmed: result.confirmed,
                        waitlisted: result.waitlisted,
                        min: min,
                        max: max,
                        state: 1
                    });
                },
                (error) => {console.log('network error')}
            );
        } else {
            this.props.history.push('/');
        }
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

        // lets just stop after 20 iterations incase we encounter something broken
        let i = 0;
        while ('' + year + month != window.sessionStorage.getItem('last_term') && i++ < 20) {
            options.push(<option>{'' + year + month}</option>);

            let tmp = month + 40;
            month = tmp % 120;
            year += Math.floor(tmp / 120);
        }

        return options;
    }
    
    render() {
        let authed = this.props.authenticated;

        if (authed === true && this.state.state === 1) {
            return (
                <div>
                    <div className = 'heading vertical'>
                        <p>Week</p>
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
        } else {
            return <p>week, you aint authenticated</p>;
        }
    }
}

export default withRouter(Week);