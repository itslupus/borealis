import React from 'react';

export default class Grades extends React.Component {
    constructor(props) {
        super(props);

        this.state = {
            display_courses: false,
            data: ''
        }

        this.search = this.search.bind(this);
        this.back = this.back.bind(this);
    }

    search(event) {
        event.preventDefault();

        let term = event.target.term.value;
        let course = event.target.course.value.toUpperCase();
        let split = course.split(' ');

        if (split.length !== 2 || course.length > 10) {
            return;
        }

        this.setState({display_courses: true, data: 'loading...'});

        let post_data = new URLSearchParams();
        post_data.append('course_code', course);
        post_data.append('term', term);

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
                this.setState({data: JSON.stringify(data)});
            },
            (error) => {
                this.setState({data: 'network error'});
            }
        );
    }

    back(event) {
        event.preventDefault();

        this.setState({display_courses: false});
    }

    render() {
        let authed = this.props.authenticated;

        if (authed === true) {
            if (this.state.display_courses === false) {
                return (
                    <form onSubmit = {this.search} autoComplete = 'off'>
                        <input type = 'text' name = 'course' placeholder = 'SUBJ 1234'></input>
                        <input type = 'text' name = 'term' placeholder = '202010'></input>
                        <input type = 'submit' value = 'search'></input>
                    </form>
                );
            } else {
                return (
                    <div>
                        <button onClick = {this.back}>back</button>
                        <p>{this.state.data}</p>
                    </div>
                );
            }
        } else {
            return <p>grades, you aint authenticated</p>;
        }
    }
}