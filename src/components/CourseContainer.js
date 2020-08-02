import React from 'react';

import Dropdown from './Dropdown';

export default class CourseContainer extends Dropdown {
    constructor(props) {
        super(props);

        this.state = {
            status: 0,
            data: 'waiting'
        }

        this.load = this.load.bind(this);
    }

    load(event) {
        super.toggle(event);

        if (this.state.status === 0) {
            this.setState({data: 'loading'});

            let post_data = new URLSearchParams();
            post_data.append('term', 202090);
            post_data.append('course_code', this.props.name);
    
            fetch('api/FetchCourse.php', {
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
                    this.setState({data: JSON.stringify(data), status: 1});
                },
                (error) => {
                    this.setState({data: 'network error'});
                }
            );
        }
    }

    render() {
        return (
            <div>
                <div className = 'dropdown-header' onClick = {this.load}>
                    {this.props.name}
                </div>
                <div className = 'dropdown-content hidden'>
                    {this.state.data}
                </div>
            </div>
        )
    }
}