<?php
function translate($language){
    $translation['English']['email'] = "Email";
    $translation['English']['pass'] = "Password";
    $translation['English']['login'] = "Log In";
    $translation['English']['nameF'] = "First Name";
    $translation['English']['nameL'] = "Last Name";
    $translation['English']['dob'] = "DOB";
    $translation['English']['find'] = "Find Patient";
    $translation['English']['new'] = "New Patient";
    
    return $translation[$language];

}

?>