import React from 'react';

export default class Dropdown extends React.Component {
    constructor(props) {
        super(props);
    
        this.toggle = this.toggle.bind(this);
    }

    toggle(event) {
        let content = event.target.nextElementSibling;
        let content_classes = content.classList;
        content_classes.toggle('hidden');
    }
}