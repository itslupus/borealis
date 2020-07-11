import React from 'react';
import {withRouter} from 'react-router-dom';

class Grades extends React.Component {
    constructor(props) {
        super(props);

        this.state = {
            data: ''
        }

        this.fetch_data = this.fetch_data.bind(this);
    }

    fetch_data() {
        if (this.props.authenticated === true) {
            this.setState({data: 'loading...'});

            let post_data = new URLSearchParams();
            post_data.append('term', 202010);
    
            fetch('api/FetchGrade.php', {
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
        } else {
            this.props.history.push('/');
        }
    }

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
            let cookie = document.cookie;
            let data = this.state.data;

            return (
                <div><p>grades '{cookie}'</p><p>{data}</p></div>
            );
        } else {
            return <p>grades, you aint authenticated</p>;
        }
    }
}

export default withRouter(Grades);

/*
{"result":{"grades":[{"subj":"COMP","course":"3010","section":"A01","grade":"B","hours":"3.000"},{"subj":"COMP","course":"3350","section":"A01","grade":"A","hours":"3.000"},{"subj":"COMP","course":"3430","section":"A02","grade":"A","hours":"3.000"},{"subj":"SCI","course":"2000","section":"T03","grade":"B","hours":"3.000"}],"gpa":[{"attempt":"12.000","earned":"12.000","hours":"12.000","quality":"42.00","gpa":"3.50"},{"attempt":"90.000","earned":"81.000","hours":"90.000","quality":"276.00","gpa":"3.07"},{"attempt":"0.000","earned":"0.000","hours":"0.000","quality":"0.00","gpa":"0.00"},{"attempt":"90.000","earned":"81.000","hours":"90.000","quality":"276.00","gpa":"3.07"}]}}
*/