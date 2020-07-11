import React from 'react';
import {withRouter} from 'react-router-dom';

class Welcome extends React.Component {
    constructor(props) {
        super(props);

        this.state = {
            id: false,
            password: false,
            network: false,
            state_text: ''
        };
        this.login = this.login.bind(this);
    }

    login(event) {
        event.preventDefault();

        const id = event.target.id.value;
        const password = event.target.password.value;

        let is_valid = true;
        
        if (id === '' || id.length !== 7 || isNaN(id) === true) {
            this.setState({id: true});
            is_valid = false;
        } else {
            this.setState({id: false});
        }

        if (password === '') {
            this.setState({password: true});
            is_valid = false;
        } else {
            this.setState({password: false});
        }

        if (is_valid === false)
            return;

        this.setState({state_text: 'logging in...'});

        let post_data = new URLSearchParams();
        post_data.append('id', id);
        post_data.append('password', password);

        fetch('api/Authenticate.php', {
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
                if (data.status === 1) {
                    this.setState({state_text: 'wrong id/password'});
                } else {
                    this.props.set_auth_state(true);
                    this.props.history.push('/home');
                }
            },
            (error) => {
                this.setState({state_text: 'network error'});
            }
        );
    }

    componentDidUpdate(prev_props) {
        if (this.props.location.key !== prev_props.location.key) {
            if (this.props.authenticated === true) {
                this.props.history.push('/home');
            }
        }
    }

    componentDidMount() {
        if (this.props.authenticated === true) {
            this.props.history.push('/home');
        }
    }

    render() {
        const form = (
            <form onSubmit = {this.login} autoComplete = 'off'>
                <input type = 'text' name = 'id' placeholder = 'student number'></input><br />
                <input type = 'password' name = 'password' placeholder = 'password'></input><br /><br />
                <input type = 'submit' value = 'login'></input>
            </form>
        );

        let state = <p>{this.state.state_text}</p>;

        let err_msg = [];

        if (this.state.id === true) {
            err_msg.push(<p>invalid id</p>);
        }
        
        if (this.state.password === true) {
            err_msg.push(<p>invalid password</p>);
        }

        if (this.state.network === true) {
            err_msg.push(<p>invalid network</p>);
        }

        return [form, state, err_msg];
    }
}

export default withRouter(Welcome);