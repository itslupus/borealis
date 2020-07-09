import React from 'react';

import {is_authenticated} from './Auth';
export default class Home extends React.Component {
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

            fetch('api/FetchAccSummary.php', {
                method: 'POST'
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

/*
{"result":{"balance":"$0.00","items":[{"desc":"Fac of Science Tuition","charge":"$1,745.04","payment":" "},{"desc":"Lab Fees","charge":"$39.26","payment":" "},{"desc":"Library Fee","charge":"$22.82","payment":" "},{"desc":"Registration Fee","charge":"$22.82","payment":" "},{"desc":"Sport & Recreation Fee","charge":"$86.64","payment":" "},{"desc":"Student Organization Fees","charge":"$125.84","payment":" "},{"desc":"Student Services Fee","charge":"$22.82","payment":" "},{"desc":"Tech Fee","charge":"$78.12","payment":" "},{"desc":"U-PASS fee","charge":"$136.25","payment":" "},{"desc":"Payment- Web Banking","charge":" ","payment":"$2,363.61"}]}}
*/