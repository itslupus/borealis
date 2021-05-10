'use strict';

require('dotenv').config();

const fetch = require('node-fetch');

/**
 * Builds a node-fetch promise
 * 
 * @param {*} uri the URI to access
 * @param {*} args fetch API arguments
 * @returns node-fetch Promise
 */
module.exports = (uri, args) => {
    // copy args
    let fetch_params = Object.assign({}, args);
    
    // init header object if not existant
    fetch_params.headers = fetch_params.headers || {};

    // add test id cookie
    fetch_params.headers['Cookie'] = fetch_params.headers['Cookie'] || [];
    fetch_params.headers['Cookie'].push('TESTID=set');
    
    // add user agent
    fetch_params.headers['User-Agent'] = 'Mozilla/5.0 (Windows NT 10.0; rv:78.0) Gecko/20100101 Firefox/78.0';

    return fetch(process.env.BANNER_HOST + uri, fetch_params);
}