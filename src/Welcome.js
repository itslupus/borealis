import React from 'react';

import {is_authenticated} from './Auth';

export default class Welcome extends React.Component {
    constructor(props) {
        super(props);

        this.state = {
            errors: {
                username: false,
                password: false,
                network: false
            },
            state_text: '',
            authenticated: is_authenticated()
        };
        this.login = this.login.bind(this);
    }

    login(event) {
        event.preventDefault();

        const id = event.target.id.value;
        const password = event.target.password.value;

        let is_valid = true;

        if (id === '' || id.length !== 7 || isNaN(id) === true) {
            this.setState({errors: {username: true}});
            is_valid = false;
        } else {
            this.setState({errors: {username: false}});
        }

        if (password === '') {
            this.setState({errors: {password: true}});
            is_valid = false;
        } else {
            this.setState({errors: {password: false}});
        }

        if (is_valid === false)
            return;

        let post_data = new URLSearchParams();
        post_data.append('id', id);
        post_data.append('password', password);

        this.setState({state_text: 'logging in...'});

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
                // invalid login info
                if (data.status === 1) {
                    this.setState({state_text: 'invalid id or password'});
                } else {
                    this.props.history.push('/home');
                    this.setState({authenticated: true});
                }
            },
            (error) => {
                this.setState({errors: {network: true}});
            }
        );
    }

    componentDidMount() {
        if (this.state.authenticated === true) {
            this.props.history.push('/home');
        }
    }

    render() {
        const form = (
            <form onSubmit = {this.login} autoComplete = 'off'>
                <input type = 'text' name = 'id' placeholder = 'student number'></input>
                <input type = 'password' name = 'password' placeholder = 'password'></input>
                <input type = 'submit' value = 'login'></input>
            </form>
        );

        let state = <p>{this.state.state_text}</p>;

        let err_msg = [];

        if (this.state.errors.username === true) {
            err_msg.push(<p>invalid username</p>);
        }
        
        if (this.state.errors.password === true) {
            err_msg.push(<p>invalid password</p>);
        }

        if (this.state.errors.network === true) {
            err_msg.push(<p>invalid network</p>);
        }

        return [form, state, err_msg];
    }
}