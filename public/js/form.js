$(document).ready(function(){

    $('#form-signup').validate({
       rules: {
           username: 'required',
           email: {
               required: true,
               email: true,
               errorClass: 'invalid-feedback'
           },
           password: {
               required: true,
               minlength: 6,
               validPassword: true
           }
       }
    });

    $('#inputPassword').hideShowPassword({
        show: false,
        innerToggle: 'focus',
        toggle: {
            className: 'btn btn-sm',
            offset: 5
        }
    });

    $('#form-contact').validate({
        rules: {
            email: {
                required: true,
                email: true,
                errorClass: 'invalid-feedback'
            },
            name: {
                required: true,
                minlength: 1,
            },
            number: {
                required: false,
                digits: true
            }
        }
    });


});

Dropzone.options.formDropzone = {
    maxFiles: 1,
    accept: function(file, done) {
        console.log("uploaded");
        done();
    },
    init: function() {
        this.on("maxfilesexceeded", function(file) {
            alert("Please upload only one file at a time!");
        });
    }
};

