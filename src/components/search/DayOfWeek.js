import React from 'react';

export default class DayOfWeek extends React.Component {
    constructor(props) {
        super(props);

        let days = {
            S: {state: false, display: 'S'},
            M: {state: false, display: 'M'},
            T: {state: false, display: 'T'},
            W: {state: false, display: 'W'},
            R: {state: false, display: 'T'},
            F: {state: false, display: 'F'},
            U: {state: false, display: 'S'},
        }
        
        for (let day of this.props.days.split('')) {
            try {
                days[day].state = true;
            } catch (Exception) {
                console.log('search: invalid date');
            }
        }

        this.state = {week: days};
    }

    render() {
        return (
            <div className = 'vertical'>
                {
                    Object.keys(this.state.week).map(day => {
                        return this.state.week[day].state === true
                            ? <div style = {{background: 'white', color: 'black', padding: '5px'}}>{this.state.week[day].display}</div>
                            : <div style = {{padding: '5px'}}>{this.state.week[day].display}</div>
                    })
                }
            </div>
        );
    }
}