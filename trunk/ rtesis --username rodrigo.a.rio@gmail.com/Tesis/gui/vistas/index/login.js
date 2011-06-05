$(document).ready(function(){
    
    $("#userForm").validate({

        rules: {
            companyContactFirstName: "required",
            companyContactLastName: "required",
            companyEmail: {required: true, email: true},
            companyName: "required",
            termsAndConditions: "required",
            companyLoginPassword: {
                required: true,
                password: "#companyEmail"
            },
            companyLoginPassword2: {
                equalTo: "#companyLoginPassword"
            }
        },

        messages: {
            companyContactFirstName: validatorMessage("required", "first name"),
            companyContactLastName: validatorMessage("required", "last name"),
            companyEmail: {
                required: validatorMessage("required", "email address"),
                email: validatorMessage("valid", "email address")
            },
            companyName: validatorMessage("required", "company name"),
            termsAndConditions: "You have to accept our terms and conditions",
            companyLoginPassword: {
               required: validatorMessage("required", "password"),
               password: validatorMessage("password")
            },
            companyLoginPassword2: validatorMessage("equalPasswords")
        },

        errorPlacement: function(label, element) {
            if (element.attr('id') == "termsAndConditions") {
                label.insertAfter("#termsAndConditions");
            } else {
                label.insertAfter(element);
            }
        }
    });

    $("#userForm").submit(function(){
        if ($("#userForm").valid() == true) {
            // If form is valid:
            // Calculate and set MD5 password.
            hashPassword("companyLoginPassword", "companyLoginPasswordMD5");
            // Remove attribute "name" from password fields, so this inputs
            // don't will be posted.
            $("#companyLoginPassword").removeAttr("name");
            $("#companyLoginPassword2").removeAttr("name");
        }
    });
});