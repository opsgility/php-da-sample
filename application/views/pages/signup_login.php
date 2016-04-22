<?php
if($show_login) { /*Load signup form*/
    echo (isset($loginSignupForm)) ? $loginSignupForm: '';
}else{
    header("Location". base_url());
}
?>