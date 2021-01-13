<!doctype html>
<html class="no-js" lang="">
<head>
    <meta charset="utf-8">
    <meta http-equiv="x-ua-compatible" content="ie=edge">
    <title>Reset Password</title>
    <meta name="description" content="${2}">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link rel="apple-touch-icon" href="icon.png">

    <link rel="stylesheet" href="<?php echo $this->config->item('css_path') ?>bootstrap.min.css">
    <link rel="stylesheet" href="<?php echo $this->config->item('css_path') ?>fonts.css">
    <link rel="stylesheet" href="<?php echo $this->config->item('css_path') ?>main.css">
    <link rel="stylesheet" href="<?php echo $this->config->item('css_path') ?>responsive.css">
</head>
<body>
<style type="text/css">
form .error {
  color: #ff0000;
}
</style>
<div class="login-section">
    <div class="login-area">
        <div class="container-fluid">
            <div class="row">
                <div class="col-md-4 col-sm-5 col-xs-12 col-md-push-8 col-sm-push-7">
                    <div class="login-outer" id="login-block">
                        <div class="text-center"><img src="<?php echo $this->config->item('image_path') ?>/logo.png" alt=""></div>
                        <?=$this->load->view('admin/include/alert_message')?>
                        <form action="<?=base_url('admin/change_password')?>" method="post" id="login-form">
                            <div class="form-group">
                                <input type="password" class="form-control" name="password" id="password" placeholder="Password">
                            </div>
                            <div class="form-group">
                                <input type="password" class="form-control" name="cnf_password" id="cnf_password" placeholder="Confirm Password">
                            </div>
                            <div class="text-center">
                                <input type="hidden"  name="token" value="<?=$token;?>">
                                <button type="submit" class="btn btn-primary btn-blue text-white m-t-20">Submit</button>
                            </div>
                        </form>
                    </div>
                </div>
                <div class="col-md-8 col-sm-7 col-xs-12 col-md-pull-4 col-sm-pull-5">
                    <div class="login-text">
                        <h1>Lorem ipsum dolor sit amet, cons ectetur adipisicing elit,</h1>
                        <p>Lorem ipsum dolor sit amet, consectetur adipisicing elit, sed do eiusmod tempor incid idunt ut labore et dolore magna aliqua. Ut enim ad minim veniam, quis nostrud exercit ation ullamco laboris nisi ut aliquip ex ea commodo consequat. Duis aute irure dolor in reprehenderit in voluptate velit esse cillum dolore eu fugiat nulla pariatur. </p>
                    </div>
                </div>

            </div>
        </div>
    </div>
</div>
    <script src="<?php echo $this->config->item('js_path') ?>jquery-3.2.1.min.js"></script>
    <script src="<?php echo $this->config->item('js_path') ?>bootstrap.min.js"></script>
    <script src="<?php echo $this->config->item('js_path') ?>jquery.validate.min.js"></script>
    <script src="<?=$this->config->item('js_path')?>common.js"></script>
    <script type="text/javascript">
        // Wait for the DOM to be ready
        $(document).ready(function () {

            $("#login-form").validate({

                rules: {

                    password: {
                        minlength : 5,
                        required: true,
                    },
                    cnf_password: {
                        required: true,
                        minlength : 5,
                        equalTo: "#password"
                    }
                },
                messages: {
                    password: {
                        required: "Please provide a password",
                        minlength: "Your password must be at least 5 characters long"
                    },
                    cnf_password: {
                        required: "Please provide a change password",
                        minlength: "Your password must be at least 5 characters long",
                        equalTo : "Please enter the correct password"
                    },
                },

                submitHandler: function(form) {
                    form.submit();
                }
            });


            setTimeout(function() {
                $('#div_msg').hide('slow');
            }, 5000);
        });
    </script>
</body>
</html>