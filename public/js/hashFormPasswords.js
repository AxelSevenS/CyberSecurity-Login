import { SHA512 } from './sha512.js';

function onSubmit(event) {
    let target = event.target;

    // hash password fields
    let passwordFields = target.querySelectorAll('input[type=password]');
    for (let i = 0; i < passwordFields.length; i++) {
        if (passwordFields[i].value != '') {
            passwordFields[i].value = SHA512(passwordFields[i].value);
        }
    }
    return true;
}

for (let i = 0; i < document.forms.length; i++) {
    document.forms[i].addEventListener('submit', onSubmit);
}