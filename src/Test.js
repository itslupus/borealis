import React from 'react';

export default class Test extends React.Component {
    constructor(props) {
        super(props);

        this.state = {
            display_courses: false,
            data: ''
        }
    }

    render() {
        return <p>test page</p>;
    }
}