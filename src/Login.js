import React from 'react';

import {is_authenticated} from './Auth';

export default class Login extends React.Component {
    constructor(props) {
        super(props);

        this.state = {
            username_valid: true,
            password_valid: true,
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
            this.setState({username_valid: false});
            is_valid = false;
        } else {
            this.setState({username_valid: true});
        }

        if (password === '') {
            this.setState({password_valid: false});
            is_valid = false;
        } else {
            this.setState({password_valid: true});
        }

        if (is_valid === false)
            return;

        let post_data = new URLSearchParams();
        post_data.append('id', id);
        post_data.append('password', password);

        fetch('api/test.php', {
            method: 'POST',
            body: post_data
        })
        .then(response => {
            console.log(document.cookie);

            return response.json();
        })
        .then(
            (data) => {
                console.log(data);
                
                this.props.history.push('/');
                this.setState({authenticated: true});
            },
            (error) => console.log(error)
        );
    }

    render() {
        if (this.state.authenticated === true)
            return <p>already logged in</p>;

        const form = (
            <form onSubmit = {this.login} autoComplete = 'off'>
                <input type = 'text' name = 'id' placeholder = 'student number'></input>
                <input type = 'password' name = 'password' placeholder = 'password'></input>
                <input type = 'submit' value = 'login'></input>
            </form>
        );

        let err_msg = [];

        if (this.state.password_valid === false) {
            err_msg.push(<p>invalid password </p>);
        }
        
        if (this.state.username_valid === false) {
            err_msg.push(<p>invalid username </p>);
        }

        return [form, err_msg];
    }
}