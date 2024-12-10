<?php
require ('conect.php');
//include 'src/adLDAP.php';
//if(!isset($_SESSION['user'])){
//    header('Location:login.php');
//    exit();
//}
$db = new mysqli($SERVER,$USER,$PASS,$DB);


?>
<!DOCTYPE html>
<html lang="en">
    <?php include 'head_ds.php'?>
    <body class="full_width">
        
        <div id="maincontainer" class="clearfix">
            <?php include 'header.php'?>
            <div id="contentwrapper">
                <div class="main_content">
                    <div class="row">
                        <div class="col-sm-12 col-md-12">
                            <h3 class="heading">Wizard with validation</h3>
                            <div class="row">
                                    <div class="col-sm-12 col-md-12">
                            <form id="validate_wizard" class="stepy-wizzard form-horizontal">
                                <fieldset title="Personal info">
                                    <legend class="hide">Lorem ipsum dolor…</legend>
                                    <div class="formSep form-group">
                                        <label for="v_username" class="col-md-2 control-label">Username:</label>
                                        <div class="col-md-10">
                                                                            <input type="text" name="v_username" id="v_username" class="input-sm form-control">
                                                                    </div>
                                    </div>
                                    <div class="formSep form-group">
                                        <label for="v_password" class="col-md-2 control-label">Password:</label>
                                        <div class="col-md-10">
                                                                            <input type="password" name="v_password" id="v_password" class="input-sm form-control">
                                                                    </div>
                                    </div>
                                    <div class="form-group">
                                        <label for="v_email" class="col-md-2 control-label">E-mail:</label>
                                        <div class="col-md-10">
                                                                            <input type="text" name="v_email" id="v_email" class="input-sm form-control">
                                                                    </div>
                                    </div>
                                </fieldset>
                                <fieldset title="Contact info">
                                    <legend class="hide">Lorem ipsum dolor…</legend>
                                    <div class="formSep form-group">
                                        <label for="v_street" class="col-md-2 control-label">Street Address:</label>
                                        <div class="col-md-10">
                                                                            <input type="text" name="v_street" id="v_street" class="input-sm form-control">
                                                                    </div>
                                    </div>
                                    <div class="formSep form-group">
                                        <label for="v_city" class="col-md-2 control-label">City:</label>
                                        <div class="col-md-10">
                                                                            <input type="text" name="v_city" id="v_city" class="input-sm form-control">
                                                                    </div>
                                    </div>
                                    <div class="form-group">
                                        <label for="v_country" class="col-md-2 control-label">Country:</label>
                                        <div class="col-md-10">
                                                                            <input type="text" name="v_country" id="v_country" class="input-sm form-control">
                                                                    </div>
                                    </div>
                                </fieldset>
                                                    <fieldset title="Additional info">
                                    <legend class="hide">Lorem ipsum dolor…</legend>
                                                            <div class="formSep form-group">
                                        <label for="v_message" class="col-md-2 control-label">Your Message:</label>
                                        <div class="col-md-10">
                                                                            <textarea name="v_message" id="v_message" rows="3" class="form-control"></textarea>
                                                                    </div>
                                    </div>
                                    <div class="form-group">
                                        <label class="col-md-2 control-label">Newsletter:</label>
                                        <div class="col-md-10">
                                                                            <label class="radio-inline" for="newsletter_yes">
                                                                                    <input type="radio" value="yes" id="newsletter_yes" name="v_newsletter"> Yes
                                                                            </label>
                                                                            <label class="radio-inline" for="newsletter_no">
                                                                                    <input type="radio" value="no" id="newsletter_no" name="v_newsletter"> No
                                                                            </label>
                                                                    </div>
                                    </div>
                                                    </fieldset>
                                <button type="button" class="finish btn btn-primary"><i class="glyphicon glyphicon-ok"></i> Send registration</button>
                            </form>
                        </div>
                    </div>
                    </div>
                    </div>
                </div>
            </div>
        </div>
    <?php 
        include 'sidebar.php';
        include 'js_in.php';
        include 'js_fn.php';
    ?>
        <script>
            
        </script>
    </body>
</html>
