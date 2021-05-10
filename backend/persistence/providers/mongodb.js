'use strict';

require('dotenv').config();
const MongoClient = require('mongodb').MongoClient;

module.exports = class MongoDB {
    static __client;
    static __db;

    /**
     * Creates a MongoDB singleton object
     * 
     * @returns MongoDB database connection
     */
    static async open() {
        if (!MongoDB.client) {
            console.log('+ Creating MongoDB connection...');

            const url = `mongodb://${process.env.MONGODB_USERNAME}:${process.env.MONGODB_PASSWORD}@${process.env.MONGODB_HOST}:${process.env.MONGODB_PORT}/${process.env.MONGODB_DATABASE}`;

            MongoDB.__client = await MongoClient.connect(url, {useUnifiedTopology: true});
            MongoDB.__db = MongoDB.__client.db(process.env.MONGODB_DATABASE);
        }

        return MongoDB.__db;
    }

    /**
     * Adds a new user document to the database
     * 
     * @param {number} stu_num the student number
     * @param {string} ip_addr IPv4/6 of the client
     * @param {string} session_id the session ID that Banner gives
     * 
     * @returns Promise of MongoDB insert result
     */
    add_user(stu_num, ip_addr, session_id) {
        const users = MongoDB.__db.collection('users');

        return users.insertOne({
            stu_num: stu_num,
            ip_addr: ip_addr,
            session_id: session_id,
            last_login: Date.now()
        });
    }

    /**
     * Finds a user document by student number
     * 
     * @param {number} stu_num the student number
     * @returns Promise of search result
     */
    get_user(stu_num) {
        const users = MongoDB.__db.collection('users');

        return users.findOne({stu_num: stu_num});
    }
}