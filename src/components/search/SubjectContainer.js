import React from 'react';

import CourseContainer from './CourseContainer';
import Dropdown from '../Dropdown';

export default class SubjectContainer extends Dropdown {
    render() {
        let data = this.props.courses;

        return (
            <div>
                <div className = 'dropdown-header' onClick = {super.toggle}>
                    {this.props.name}
                </div>
                <div className = 'dropdown-content hidden'>
                    {
                        Object.keys(data).map((name) => {
                            return <CourseContainer name = {name} />;
                        })
                    }
                </div>
            </div>
        )
    }
}