'use strict';


module.exports = {
    /**
     * Validate student number structure
     * 
     * @param {number} student_number 
     * @returns true if student number is of valid format
     */
    validate_student_number(student_number) {
        if (student_number.toString().length !== 7 || isNaN(student_number)) return false;

        return true;
    },

    /**
     * Validate password structure
     * 
     * @param {string} password
     * @returns true if password is of valid format
     */
    validate_password(password) {
        if (password.length < 6 || password.length > 10) return false;

        // regex to check if string contains a digit (\d)
        if (/\d/.test(password)) return true;

        return false;
    }
};