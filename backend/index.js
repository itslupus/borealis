'use strict';

require('dotenv').config();
const app = require('express')();

// required for X-Forwarded-* headers
app.set('trust proxy', true);

// required for parsing of JSON body parameters
app.use(express.json());

// basic validation of .env PORT
if (process.env.PORT === undefined || process.env.PORT === '') {
    console.log('! Invalid .env file (missing PORT?)');
} else {
    app.listen(process.env.PORT, () => {
        console.log('+ Backend ready on port ' + process.env.PORT);
    })
}
