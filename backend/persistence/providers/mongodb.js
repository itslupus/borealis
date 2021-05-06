'use strict';

require('dotenv').config();
const MongoClient = require('mongodb').MongoClient;

module.exports = class MongoDB {
    static __client;
    static __db;

    static async open() {
        if (!MongoDB.client) {
            console.log('+ Creating MongoDB connection...');

            const url = `mongodb://${process.env.MONGODB_USERNAME}:${process.env.MONGODB_PASSWORD}@${process.env.MONGODB_HOST}:${process.env.MONGODB_PORT}/${process.env.MONGODB_DATABASE}`;

            MongoDB.__client = await MongoClient.connect(url, {useUnifiedTopology: true});
            MongoDB.__db = MongoDB.__client.db(process.env.MONGODB_DATABASE);
        }

        return MongoDB.__db;
    }
}