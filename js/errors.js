

function show_errors(variable_name, error_message) {
    
    document.getElementById(variable_name).style.display = "block";
    document.getElementById(variable_name).innerHTML = error_message;

    console.log(variable_name);

    console.log(error_message);
}