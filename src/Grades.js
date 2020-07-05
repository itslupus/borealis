import React from 'react';

import {is_authenticated} from './Auth';

export default class Grades extends React.Component {
    constructor(props) {
        super(props);

        this.state = {
            authenticated: false,
            data: ''
        }

        this.fetch_data = this.fetch_data.bind(this);
    }

    fetch_data() {
        let is_auth = is_authenticated();
        this.setState({authenticated: is_auth});

        if (is_auth === true) {
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
                    console.log('network error')
                }
            );
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
        let authed = this.state.authenticated;

        if (authed === true) {
            let cookie = document.cookie;
            let data = this.state.data;

            return (
                <div><p>home '{cookie}'</p><p>{data}</p></div>
            );
        } else {
            return <p>home, you aint authenticated</p>;
        }
    }
}