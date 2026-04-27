window.addEventListener("load", function () {

    
    const myForm = document.getElementById("myForm");
    const errorDisplay = document.getElementById("error-message");

 
    myForm.addEventListener("submit", function (event) {

        
        const email = document.getElementById("email").value.trim();
        const atPosition = email.indexOf("@");
        const dotPosition = email.lastIndexOf(".");
        const emailLength = email.length;

        
        if (
            email === "" ||
            atPosition < 1 ||
            dotPosition === -1 ||
            dotPosition < atPosition + 2 ||
            dotPosition === emailLength - 1
        ) {
            // Display error message and prevent submission
            errorDisplay.childNodes[0].innerHTML =
                "Email must have form a@b.c<br>Example: username@domain.tld";
            errorDisplay.style.display="block";
            event.preventDefault();
        } else {
            // Clear error message if valid
            errorDisplay.childNodes[0].innerHTML = "";
            errorDisplay.style.display="none";
        }
    });

});