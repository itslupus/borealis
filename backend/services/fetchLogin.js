'use strict';

const fetch = require('./fetch');

/**
 * POSTs the login endpoint with provided student number and password
 * 
 * @param {number} stu_num 
 * @param {string} password 
 * @returns false if invalid creds, otherwise returns session ID
 */
module.exports = async (stu_num, password) => {
    const params = new URLSearchParams({sid: stu_num, PIN: password});
    
    let result = false;

    await fetch('/twbkwbis.P_ValLogin', {
        referrer: process.env.BANNER_HOST + '/twbkwbis.P_WWWLogin',
        method: 'POST',
        body: params
    })
    .then((data) => {
        const headers = data.headers;

        // umanitoba's aurora sends a response of 198 bytes on good login
        if (data.ok && headers.get('Content-Length') < 250) {
            // search through all cookies since we cant assume first cookie is sessid
            for (const cookie of headers.get('Set-Cookie').split(', ')) {
                if (cookie.substring(0, 6) === 'SESSID') {
                    result = cookie.split('=')[1];
                    break;
                }
            }
        }
    });

    return result;
}