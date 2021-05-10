'use strict';

const fetchLogin = require('../services/fetchLogin');
const {validate_student_number, validate_password} = require('../services/validateCreds');

/**
 * This route authenticates the user against the Banner API.
 * 
 * Expected body parameters:  
 * {stu_num: 1234567, password: 'abc'} 
 */
module.exports = async (request, response) => {
    const body = request.body;
    const body_obj_wrapper = Object.keys(body);

    // check if we have two keys: stu_num and password
    if (!(body_obj_wrapper.includes('stu_num') && body_obj_wrapper.includes('password')) || body_obj_wrapper.length !== 2) {
        response.status(400).end();
        return;
    }

    // check if the parameters are valid (length, required numbers)
    if (!validate_student_number(body.stu_num) || !validate_password(body.password)) {
        response.status(401).end();
        return;
    }

    // perform API query
    const result = await fetchLogin(body.stu_num, body.password);

    // bad creds
    if (result === false) {
        response.status(401).end();
        return;
    }

    response.status(201).end();
};