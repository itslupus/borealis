'use strict';

const {validate_student_number, validate_password} = require('../services/validateCreds');

describe('Test student number validation', () => {
    test('Blank student number', () => {
        expect(validate_student_number('')).toBeFalsy();
    });

    test('Student number of length < 7', () => {
        expect(validate_student_number(12345)).toBeFalsy();
    });

    test('Student number of length > 7', () => {
        expect(validate_student_number(123456789)).toBeFalsy();
    });

    test('Student number contains non-numeric values', () => {
        expect(validate_student_number('123abc7')).toBeFalsy();
    });

    test('Student number is exclusively non-numeric values', () => {
        expect(validate_student_number('abcdefg')).toBeFalsy();
    });

    test('Valid student number', () => {
        expect(validate_student_number(7812345)).toBeTruthy();
    });

    test('Valid student number (as string)', () => {
        expect(validate_student_number('7812345')).toBeTruthy();
    });
});

describe('Test password validation', () => {
    test('Blank password', () => {
        expect(validate_password('')).toBeFalsy();
    });

    test('Password length < 6', () => {
        expect(validate_password('pass')).toBeFalsy();
    });

    test('Password length > 10', () => {
        expect(validate_password('passwords suck')).toBeFalsy();
    });

    test('Password valid length, but no number', () => {
        expect(validate_password('password')).toBeFalsy();
    });

    test('Password valid', () => {
        expect(validate_password('p@^7LF&9su')).toBeTruthy();
    });
});