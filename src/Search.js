import React from 'react';
import {withRouter} from 'react-router-dom';

class Search extends React.Component {
    constructor(props) {
        super(props);

        this.state = {
            search_text: '',
            timeout: 0,
            select_subjects: [],
            fetch_subjects: [],
            status: 'waiting',
            data: {}
        }

        this.fetch = this.fetch.bind(this);
        this.search = this.search.bind(this);
        this.add_subject = this.add_subject.bind(this);
        this.remove_subject = this.remove_subject.bind(this);
    }

    fetch(event) {
        event.preventDefault();

        if (this.props.authenticated === true) {
            this.setState({status: 'loading'});

            let post_data = new URLSearchParams();
            post_data.append('term', 202090);
            
            for (let pair of this.state.fetch_subjects) {
                post_data.append('subjects[]', pair[0]);
            }
    
            fetch('api/FetchSubjectCourses.php', {
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
                    this.setState({status: 'done', data: data});
                },
                (error) => {
                    this.setState({status: 'network error'});
                }
            );
        } else {
            this.props.history.push('/');
        }
    }

    search(event) {
        event.preventDefault();

        if (this.state.timeout !== 0) {
            clearTimeout(this.state.timeout);
        }

        this.setState({
            search_text: event.target.value,
            timeout: setTimeout(() => {
                let pushed_subjects = [];

                if (this.state.search_text !== '' && this.state.search_text !== ' ') {
                    let subjects = {
                        'ACC': 'Accounting',
                        'ABIZ': 'AgBusiness and AgEconomics',
                        'AGRI': 'Agriculture',
                        'ANSC': 'Animal Science',
                        'ANTH': 'Anthropology',
                        'ARCG': 'Architecture Interdisciplinary',
                        'ARTS': 'Arts Interdisciplinary',
                        'ASTR': 'Astronomy',
                        'BGEN': 'Biochem. and Medical Genetics',
                        'BIOL': 'Biological Sciences',
                        'BME': 'Biomedical Engineering',
                        'BIOE': 'Biosystems Engineering',
                        'CDSB': 'Canadian Studies St. Boniface',
                        'CATH': 'Catholic Studies',
                        'CHEM': 'Chemistry',
                        'CIVL': 'Civil Engineering',
                        'CLAS': 'Classical Studies',
                        'CHSC': 'Community Health Sciences',
                        'COMP': 'Computer Science',
                        'DENT': 'Dentistry',
                        'ECON': 'Economics',
                        'EDUA': 'Education Admin, Fndns & Psych',
                        'EDUB': 'Education Curric, Tchg, & Lrng',
                        'ECE': 'Electr. and Computer Engin.',
                        'ENG': 'Engineering',
                        'ENGL': 'English',
                        'ENTR': 'Entrepreneurship/Small Bus.',
                        'ENVR': 'Environment',
                        'EER': 'Environment, Earth & Resources',
                        'EVDS': 'Environmental Design',
                        'EVIE': 'Environmental Interior Environ',
                        'FMLY': 'Family Social Sciences',
                        'FILM': 'Film Studies',
                        'FIN': 'Finance',
                        'FAAH': 'Fine Art, Art History Courses',
                        'FA': 'Fine Art, General Courses',
                        'FRAN': 'Francais St. Boniface',
                        'FREN': 'French',
                        'GMGT': 'General Management',
                        'GEOG': 'Geography',
                        'GEOL': 'Geological Sciences',
                        'GRMN': 'German',
                        'GRAD': 'Graduate Studies',
                        'HEAL': 'Health Studies',
                        'HIST': 'History',
                        'HNSC': 'Human Nutritional Sciences',
                        'HRIR': 'Human Res. Mgmt/Indus Relat.',
                        'IDM': 'Interdisciplinary Management',
                        'IMED': 'Interdisciplinary Medicine',
                        'IDES': 'Interior Design',
                        'INTB': 'International Business',
                        'KPER': 'Kinesio, Phys Ed, & Recreation',
                        'KIN': 'Kinesiology',
                        'LABR': 'Labour Studies',
                        'LARC': 'Landscape Architecture',
                        'LING': 'Linguistics',
                        'MGMT': 'Management (Extended Ed.)',
                        'MIS': 'Management Info. Systems',
                        'MSCI': 'Management Science',
                        'MKT': 'Marketing',
                        'MATH': 'Mathematics',
                        'MECG': 'Mech. Engineering Graduate',
                        'MMIC': 'Medical Microbiology',
                        'REHB': 'Medical Rehabilitation',
                        'MBIO': 'Microbiology',
                        'NATV': 'Native Studies',
                        'NURS': 'Nursing',
                        'OT': 'Occupational Therapy',
                        'OPM': 'Operations Management',
                        'PATH': 'Pathology',
                        'PHAC': 'Pharmacology',
                        'PHRM': 'Pharmacy',
                        'PHIL': 'Philosophy',
                        'PT': 'Physical Therapy',
                        'PAEP': 'Physician Assistant Education',
                        'PHYS': 'Physics',
                        'POL': 'Polish',
                        'POLS': 'Political Studies',
                        'PSYC': 'Psychology',
                        'REC': 'Recreation Studies',
                        'RLGN': 'Religion',
                        'SWRK': 'Social Work',
                        'SOC': 'Sociology',
                        'STAT': 'Statistics',
                        'SCM': 'Supply Chain Management',
                        'TRAD': 'Traduction (St. Boniface)',
                        'UKRN': 'Ukrainian',
                        'WOMN': 'Women\'s and Gender Studies'
                    };


                    let parsed_query = this.state.search_text.toLowerCase().trim();
                    for (let subj_short in subjects) {
                        let parsed_subj = subjects[subj_short].toLowerCase();

                        if (parsed_subj.includes(parsed_query)) {
                            pushed_subjects.push([subj_short, subjects[subj_short]]);

                            if (pushed_subjects.length === 5) break;
                        }
                    }
                }

                this.setState({select_subjects: pushed_subjects});
            }, 500)
        });
    }

    add_subject(event) {
        let list_element = event.target;
        let attr = list_element.getAttribute('short');
        
        if (attr !== null) {
            let update_fetch = this.state.fetch_subjects || [];

            let exists = false;
            for (let pair of update_fetch) {
                if (pair[0] === attr) {
                    exists = true
                    break;
                };
            }

            if (!exists) {
                update_fetch.push([attr, list_element.innerText]);
            }

            this.setState({
                search_text: '',
                select_subjects: [],
                fetch_subjects: update_fetch
            });

            document.getElementById('input').value = '';
        }
    }

    remove_subject(event) {
        let list_element = event.target;
        let attr = list_element.getAttribute('short');

        if (attr !== null) {
            let update_fetch = this.state.fetch_subjects || [];

            let index = 0;
            for (let pair of update_fetch) {
                if (pair[0] === attr) {
                    update_fetch.splice(index, 1);
                    break;
                };

                index++;
            }

            this.setState({fetch_subjects: update_fetch});
        }
    }

    render() {
        let data = {};
        if (this.state.data.result !== undefined && this.state.data.result.subjects !== undefined)
            data = this.state.data.result.subjects;
        
        return (
            <div>
                <div className = 'heading'>Search</div>
                <div className = 'section vertical'>
                    <div>
                        <div>
                            <input name = 'term' type = 'text' placeholder = 'term' autoComplete = 'off' defaultValue = '202090'></input>
                            <input id = 'input' name = 'subject' type = 'text' placeholder = 'subject' autoComplete = 'off' onChange = {this.search}></input>
                            <div>
                                <ul>
                                    {
                                        this.state.select_subjects.map((pair) => {
                                            return (<li short = {pair[0]} onClick = {this.add_subject}>{pair[1]}</li>)
                                        })
                                    }
                                </ul>
                            </div>
                        </div><br />
                        <div>
                            {
                                this.state.fetch_subjects.map((pair) => {
                                    return <p short = {pair[0]} onClick = {this.remove_subject}>{pair[1]}</p>;
                                })
                            }
                        </div><br />
                        <form onSubmit = {this.fetch} autoComplete = 'off'>
                            <input type = 'submit' value = 'search'></input>
                        </form>
                    </div>
                    <div id = 'courses' style = {{border: '1px solid red', width: '100%'}}>
                        <p>{this.state.status}</p>
                        {
                            Object.keys(data).map((name) => {
                                return <SubjectContainer name = {name} courses = {data[name]}/>;
                            })
                        }
                    </div>
                </div>
            </div>
        );
    }
}

export default withRouter(Search);