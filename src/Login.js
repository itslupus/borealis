import React from 'react';

export default class Login extends React.Component {
    render() {
        return (
            <form action = '/' method = 'POST'>
                <input type = 'text' name = 'id' placeholder = 'student number'></input>
                <input type = 'password' name = 'password' placeholder = 'password'></input>
                <input type = 'submit' value = 'login'></input>
            </form>
        );
    }
}