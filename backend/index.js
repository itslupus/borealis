'use strict';

require('dotenv').config();

const express = require('express');

const app = express();
const authenticate = require('./routes/authenticate');
const database_manager = require('./persistence/database');

// required for X-Forwarded-* headers
app.set('trust proxy', true);

// required for parsing of JSON body parameters
app.use(express.json());

// routes
app.post('/authenticate', authenticate);

// basic validation of .env PORT
if (process.env.PORT === undefined || process.env.PORT === '') {
    console.log('! Invalid .env file (missing PORT?)');
} else {
    app.listen(process.env.PORT, () => {
        // initialize db connection
        database_manager.get_database();
        
        console.log('+ Backend ready on port ' + process.env.PORT);
    });
}