import React from 'react';
import ReactDOM from 'react-dom';
import {Route, BrowserRouter as Router} from 'react-router-dom';

import './styles.css';

import Welcome from './Welcome';
import Home from './Home';
import Week from './Week';
import Grades from './Grades';
import Search from './Search';
import Logout from './Logout';

import Test from './Test'

import Header from './compenents/Header';
import Footer from './compenents/Footer';

import Privacy from './Privacy'
import About from './About'

class App extends React.Component {
    constructor(props) {
        super(props);

        let auth = false;
        for (let cookie of document.cookie.split('; ')) {
            if (cookie.split('=')[0] === 'token') {
                auth = true;
            }
        }

        this.state = {
            authenticated: auth
        };

        this.set_auth_state = this.set_auth_state.bind(this);
        this.logout_handler = this.logout_handler.bind(this);
    }

    set_auth_state(authed) {
        this.setState({authenticated: authed});
    }
    
    logout_handler() {
        document.cookie = 'token=; ;path=/; expires=Thu, 01 Jan 1970 00:00:01 GMT;';
        this.set_auth_state(false);
    }

    render() {
        return (
            <React.StrictMode>
                <Router>
                    <Header authenticated = {this.state.authenticated} />
                    <div className = 'app'>
                        <Route exact path = '/'
                            render = {
                                (props) => (<Welcome authenticated = {this.state.authenticated} login_handler = {this.login_handler} {...props}/>)
                            }
                        />
                        <Route path = '/home'
                            render = {
                                (props) => (<Home authenticated = {this.state.authenticated} {...props}/>)
                            }
                        />
                        <Route path = '/week'
                            render = {
                                (props) => (<Week authenticated = {this.state.authenticated} {...props}/>)
                            }
                        />
                        <Route path = '/grades'
                            render = {
                                (props) => (<Grades authenticated = {this.state.authenticated} {...props}/>)
                            }
                        />
                        <Route path = '/search'
                            render = {
                                (props) => (<Search authenticated = {this.state.authenticated} {...props}/>)
                            }
                        />
                        <Route path = '/logout'
                            render = {
                                (props) => (<Logout logout_handler = {this.logout_handler} {...props}/>)
                            }
                        />
                        <Route path = '/test'
                            render = {
                                (props) => (<Test authenticated = {this.state.authenticated} {...props}/>)
                            }
                        />
                        <Route path = '/privacy'
                            render = {
                                (props) => (<Privacy {...props}/>)
                            }
                        />
                        <Route path = '/about'
                            render = {
                                (props) => (<About {...props}/>)
                            }
                        />
                    </div>
                    <Footer />
                </Router>
            </React.StrictMode>
        );
    }
}
ReactDOM.render(
    <App />,
    document.getElementById('root')
);
