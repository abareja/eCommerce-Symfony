export const init = () => {
    const forms = document.querySelectorAll('form');

    if( forms.length === 0 ) return;

    forms.forEach(form => {
        const fields = form.querySelectorAll('.o-field');
        const button = form.querySelector('button[type="submit"]');

        if( fields.length === 0 ) return;

        fields.forEach(field => {
            const input = field.querySelector('input');

            if( !input ) return;

            input.addEventListener('change', (e) => {
                switch( input.type ) {
                    case "checkbox": validateCheckbox(field, input); break;
                    default: validateInputField(field, input); break;
                }
        
                checkForm(form);
            });
        });

        if( !button ) return;

        button.addEventListener('click', (e) => {
            e.preventDefault();
            validateAllFields(form);
            let valid = checkForm(form);

            if( valid ) {
                form.submit();
            }
        });
  
        initPasswordFields(form);
    });
}

const validateInputField = (field, input) => {
    const validationType = field.dataset.validation;
    const value = input.value;
    let valid = false;

    const firstNameReg = /^(?=.{1,50}$)[a-z]+(?:['_.\s][a-z]+)*$/i;
    const lastNameReg = /^(?=.{1,50}$)[a-z]+(?:['_.\s][a-z]+)*$/i;
    const emailReg = /^(([^<>()[\]\\.,;:\s@"]+(\.[^<>()[\]\\.,;:\s@"]+)*)|(".+"))@((\[[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}\])|(([a-zA-Z\-0-9]+\.)+[a-zA-Z]{2,}))$/;
    const passwordReg = /^(?=.*\d)(?=.*[a-z])(?=.*[A-Z])(?=.*[^a-zA-Z0-9])(?!.*\s).{8,15}$/;

    if( !validationType ) return;

    switch( validationType ) {
        case "required": 
            if( value !== "" ) valid = true; else valid = false;
            break;
        case "firstname": 
            if( value.match(firstNameReg) ) valid = true; else valid = false;
            break;
        case "lastname": 
            if( value.match(lastNameReg) ) valid = true; else valid = false;
            break;
        case "email": 
            if( value.match(emailReg) ) valid = true; else valid = false;
            break;
        case "password": 
            if( value.match(passwordReg) ) valid = true; else valid = false;
            break;
    }

    if( !valid ) {
        field.classList.add('is-invalid');
        field.classList.remove('is-valid');
    } else {
        field.classList.add('is-valid');
        field.classList.remove('is-invalid');
    }
}

const validateCheckbox = (field, input) => {
    if( field.dataset.validation == "required" ) {
        if( !input.checked ) {
            field.classList.add('is-invalid');
            field.classList.remove('is-valid');
        } else {
            field.classList.add('is-valid');
            field.classList.remove('is-invalid');
        }
    }
}

const validateAllFields = (form) => {
    const fields = form.querySelectorAll('.o-field');

    if( fields.length === 0 ) return;

    fields.forEach(field => {
        const input = field.querySelector('input');

        if( !input ) return;

        switch( input.type ) {
            case "checkbox": validateCheckbox(field, input); break;
            default: validateInputField(field, input); break;
        }
    });
}

const checkForm = (form) => {
    const invalidFields = form.querySelectorAll('.o-field.is-invalid');
    const button = form.querySelector('button[type="submit"]');
    let valid = false;

    if( invalidFields.length !== 0 ) {
        button.disabled = true;
        valid = false;
    } else {
        button.disabled = false;
        valid = true;
    }

    return valid;
}


const initPasswordFields = (form) => {
    const fields = form.querySelectorAll('.o-field');

    if( fields.length === 0 ) return;

    fields.forEach(field => {
        const input = field.querySelector('input[type="password"]');

        if( !input ) return;

        input.addEventListener('click', (e) => {
            var rect = e.target.getBoundingClientRect();
            var x = e.clientX - rect.right;

            if(x >= -30) {
                if( input.type === "password" ) {
                    input.type = "text";
                    field.classList.add('show-password');
                } else {
                    input.type = "password";
                    field.classList.remove('show-password');
                }
            }
        });
    });
} 
