import { SHA512 } from './sha512.js';

function onSubmit(event) {
    let target = event.target;

    // hash password fields
    let passwordFields = target.querySelectorAll('input[type=password]');
    for (let i = 0; i < passwordFields.length; i++) {

        if (passwordFields[i].value.length < 8) {
            alert('Password must be at least 8 characters long.');
            event.preventDefault();
            return false;
        } 

        const lowerCase = new RegExp(/[a-z]/);
        if (!lowerCase.test(passwordFields[i].value)) {
            alert('Password must contain at least one lower case letter.');
            event.preventDefault();
            return false;
        }

        const upperCase = new RegExp(/[A-Z]/);
        if (!upperCase.test(passwordFields[i].value)) {
            alert('Password must contain at least one upper case letter.');
            event.preventDefault();
            return false;
        }  

        const numbers = new RegExp(/[0-9]/);
        if (!numbers.test(passwordFields[i].value)) {
            alert('Password must contain at least one number.');
            event.preventDefault();
            return false;
        }
        
        const specialCharacters = new RegExp(/[~`!#$%\^&*+=\-\[\]\\';,/{}|\\":<>\?]/);
        if (!specialCharacters.test(passwordFields[i].value)) {
            alert('Password must contain at least one special character.');
            event.preventDefault();
            return false;
        }

        passwordFields[i].value = SHA512(passwordFields[i].value);
    }
    return true;
}

for (let i = 0; i < document.forms.length; i++) {
    document.forms[i].addEventListener('submit', onSubmit);
}