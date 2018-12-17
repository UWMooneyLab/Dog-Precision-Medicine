<?php

function screening(){


    # these are specific checks for the dog aging project
    # if the user has already filled out a personal form in their
    # current session, "dogs" will be a variable in $_SESSION

    # depending on the presence of "dog" one of the form's survey info will be
    # pulled from REDCap, via the redcap_api file.
	if(!isset($_SESSION["dogs"])){
        $current_view = "personal_info";
        $survey_info = get_screening_form("screening_about_you");
    } else {
        $current_view = "dog_info";
        $survey_info = get_screening_form("screening_about_dog");
    }

    $num_fields = count($survey_info);
    
    # start the html form
    print '<form method="post" id="redcap_screening_form">';
    print "<span style=\"display: none; color: #DC143C;\" class='redcap_errors' id='confirmation_message' ></span>";
    # hidden input value for POST detection later
    print '<input type="hidden" name="screening_form"/>';

    # start adding the fields and input types from the survey
    foreach (range(0, $num_fields-1) as $num) {
        if ($survey_info[$num]["field_name"]!="study_id" && $survey_info[$num]["field_name"]!="record_id"){

            # outputs the label for the input form
            print($survey_info[$num]["field_label"]);
            print "<span style=\"display: none; color: #DC143C;\" class='redcap_errors' id='error_". $survey_info[$num]["field_name"] ."' ></span>";
            print ("<br id='error_break'>");

            # This series of if elseif decides how to present the input forms depending
            # on the meta data. Currently handles, text, dropdown, radio, and yesno.
            # This will need to be expanded for all possible input variations from redcap.

            # handles simple text input
            if($survey_info[$num]["field_type"]=="text"){
                echo '<input name="'. $survey_info[$num]["field_name"] .'" type="text">';
            
            # handles dropdown and radio input. This just uses the HTML selector dropdown menus
            } elseif ($survey_info[$num]["field_type"]=="dropdown" || $survey_info[$num]["field_type"]=="radio") {

                $options = explode( "|", $survey_info[$num]["select_choices_or_calculations"]);
                
                print '<select name="'. $survey_info[$num]["field_name"] .'">';
                foreach(range(0, count($options)-1) as $opt) {
                    
                    $value = explode(", ", $options[$opt])[0];
                    $label = explode(", ", $options[$opt])[1];
                    print '<option value='. $value .'>'. $label .'</option>';
                    
                }
                print '</select><br>';
            
            # handles the yesno option from redcap. Just creates a dropdown menu.
            } elseif ($survey_info[$num]["field_type"]=="yesno") {
                print '<select name="'. $survey_info[$num]["field_name"] .'">';
                    print '<option value="1">Yes</option>';
                    print '<option value="0">No</option>';
                print '</select><br>';
            }
            print "<br>";
        }
    }

    print '<input type="hidden" name="redcap_screening_nonce" value="'. wp_create_nonce("redcap-screening-nonce") . '" />';
    print '<input type="submit" value="Next"/>';

print '</form>';

# listening for errors in submission process
# shows the error messages to the user
monitor_errors();

}
add_shortcode('screening_form', 'screening');


# this is the function to which the form above is posted when its submitted.
# A couple variables are added to POST, then it is sent off to REDCap as a survey submission.
function add_screening_info() {

    global $_SESSION;
    
    if (isset( $_POST["screening_form"]) && wp_verify_nonce($_POST['redcap_screening_nonce'], 'redcap-screening-nonce')) {
        
        # we are using the $_POST object as our data to send to redcap, so we remove non-redcap fields here
        unset($_POST["screening_form"]);
        unset($_POST["redcap_screening_nonce"]);

        
        if (isset($_SESSION["dogs"])) {

            $_POST["study_id"] = $_SESSION["study_id"];
            $_POST["screening_about_dog_complete"] = 2;
            $_POST["redcap_repeat_instance"] = $_SESSION["dogs"];
            $_POST["redcap_repeat_instrument"] = "screening_about_dog";
            
            $response = json_decode(add_dog_record(json_encode([$_POST], true)));

            if (array_key_exists("count", $response)) {
                $_SESSION["dogs"] += 1;

                redcap_errors()->add("confirmation_message", __($_POST["dog_name"] . "'s information submitted"));
                $GLOBALS['submission_errors'] = TRUE;
                
            } elseif (array_key_exists("error", $response)){
                handle_errors($response);
            } else {
                print_r($response);
            }

            

        } else {
            #create_owner_record($data);
            $_POST["study_id"] = get_next_record_number();
            $_POST["screening_about_you_complete"] = 2;
            
            # validation checks that the email inputs are equal
            # then it checks that REDCap hasn't thrown any errors.
            # if "count" is in the array, then submission is success.
            # and we add "dogs" to SESSION.
            if (emails_match($_POST)) {
                $response = json_decode(create_owner_record(json_encode([$_POST], true)), true);
            
                if (array_key_exists("count", $response)) {
                    $_SESSION["dogs"] = 1;
                    $_SESSION["num_dogs"] = $_POST["num_dogs_hh"];
                    $_SESSION["study_id"] = $_POST["study_id"];

                    redcap_errors()->add("confirmation_message", __("Personal information submitted"));

                    $GLOBALS['submission_errors'] = TRUE;
                    
                } elseif (array_key_exists("error", $response)){
                    handle_errors($response);
                } else {
                    print_r($response);
                }
            }
        }
    }
    //print_r($_SESSION);
}
add_action('init', 'add_screening_info');


# listening for errors in submission process
# shows the error messages to the user
function monitor_errors() {
    if (isset($GLOBALS['submission_errors'])) {
        if ($GLOBALS['submission_errors']) {
    
    
            $codes = redcap_errors()->get_error_codes();
            //print_r($codes);
            foreach($codes as $code){
                $var = $code;
                $message = redcap_errors()->get_error_message($code);
    
                echo '<head>',
                
                        '<script type="text/javascript" src="/wp-content/plugins/REDCapToWordPress Dog Project/js/src/jquery-3.2.0.js"></script>',
                        '<script type="text/javascript" src="/wp-content/plugins/REDCapToWordPress Dog Project/js/errors.js"></script>',
                    '</head>',
                    '<body>',
                            '<script type="text/javascript">',
                                'show_errors("error_'. $var .'", "'. $message .'");',
                            '</script>',
                    '</body>';
            }
        }
    }
}

# this handles the errors when REDCap returns an error.
# we just map back to the variable name and enter the error text above the input form.
function handle_errors($response) {
    $all_errors = explode("\n", $response["error"]);
                
    foreach( range(0, count($all_errors)-1) as $error) {
        
        $var_name = str_replace('"', '', explode(",", $all_errors[$error])[1]);

        $error_message = str_replace('"', '', explode(",", $all_errors[$error])[3]);
        $error_message = str_replace($var_name .' ', "", $error_message);
        
        redcap_errors()->add($var_name, __($error_message));

        $GLOBALS['submission_errors'] = TRUE;
    }
}

# checks that the two emails match.
function emails_match($post) {
    if ($post["email"] != $post["email_2"]) {
        redcap_errors()->add("email", __("Email entries should match"));
        redcap_errors()->add("email_2", __("Email entries should match"));
        $GLOBALS['submission_errors'] = TRUE;
        return false;
    } else {
        return true;
    }
}


?>