$("body").on("click", "[type=submit]", function(e) {
    var password = $("[type=password]").val();
    var email = $("[type=email]").val();
    axios.post("/login", {
        email: email,
        password: password
    })
    .then(function (response) {
        console.log(response);
    })
    .catch(function (error) {
        console.log(error);
    });
    return false;
});
