'use strict';

require('dotenv').config();
const MongoDB = require('./providers/mongodb');

module.exports = class DatabaseManager {
    static __db;

    /**
     * Gets a singleton of a database client for queries
     * 
     * @returns database client
     */
    static async get_database() {
        if (!DatabaseManager.__db) {
            console.log('+ Opening new database connection...');
            
            switch (process.env.DB_PROVIDER) {
                case 'mongodb':
                    DatabaseManager.__db = await MongoDB.open();

                    break;
                default:
                    throw 'Invalid database provider (check .env)'
            }
        }

        console.log('+ Connected to database');

        return DatabaseManager.__db;
    }
}