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
        let a = '{"result":{"balance":"$0.00","items":[{"desc":"Fac of Science Tuition","charge":"$1,745.04","payment":" "},{"desc":"Lab Fees","charge":"$39.26","payment":" "},{"desc":"Library Fee","charge":"$22.82","payment":" "},{"desc":"Registration Fee","charge":"$22.82","payment":" "},{"desc":"Sport & Recreation Fee","charge":"$86.64","payment":" "},{"desc":"Student Organization Fees","charge":"$125.84","payment":" "},{"desc":"Student Services Fee","charge":"$22.82","payment":" "},{"desc":"Tech Fee","charge":"$78.12","payment":" "},{"desc":"U-PASS fee","charge":"$136.25","payment":" "},{"desc":"Payment- Web Banking","charge":" ","payment":"$2,363.61"}]}}'
        return (
            <div>
                <div className = 'heading'>
                    test page
                </div>
                <div className = 'section'>
                    <p>{a}</p>
                </div>
            </div>
        );
    }
}