const password = document.getElementById("password");
const confirmPassword = document.getElementById("confirmPassword");
const message = document.getElementById("message");
const submitBtn = document.getElementById("submitBtn");

function checkPassword() {

    if (confirmPassword.value === "") {
        message.innerHTML = "";
        submitBtn.disabled = true;
        return;
    }

    if (password.value === confirmPassword.value) {

        message.innerHTML = "Mot de passe confirmé";
        message.classList.remove("text-danger");
        message.classList.add("text-success");

        submitBtn.disabled = false;

    } else {

        message.innerHTML = "Les mots de passe ne correspondent pas";
        message.classList.remove("text-success");
        message.classList.add("text-danger");

        submitBtn.disabled = true;
    }
}

password.addEventListener("keyup", checkPassword);
confirmPassword.addEventListener("keyup", checkPassword);

function togglePassword() {

    let input = document.getElementById("password");
    let icon = document.getElementById("eyeIcon");

    if (input.type === "password") {

        input.type = "text";

        icon.classList.remove("bi-eye");
        icon.classList.add("bi-eye-slash");

    } else {

        input.type = "password";

        icon.classList.remove("bi-eye-slash");
        icon.classList.add("bi-eye");
    }
}


function togglePasswordConfirm() {

    let input = document.getElementById("confirmPassword");
    let icon = document.getElementById("eyeIcon1");

    if (input.type === "password") {

        input.type = "text";

        icon.classList.remove("bi-eye");
        icon.classList.add("bi-eye-slash");

    } else {

        input.type = "password";

        icon.classList.remove("bi-eye-slash");
        icon.classList.add("bi-eye");
    }
}