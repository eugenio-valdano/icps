<?php
// connect to db
$dbinfo = explode("\n", file_get_contents('loginSTRIPE.txt'))[0];
$dbinfo = explode(" ", $dbinfo);
$APItest = $dbinfo[1];
$APIlive = $dbinfo[3];
?>

<!DOCTYPE html>
<html lang="en">
    <head>
        <meta http-equiv="Content-type" content="text/html; charset=utf-8" />
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <title>Secure Payment Form</title>
        <link rel="stylesheet" href="css/bootstrap-min.css">
        <link rel="stylesheet" href="css/bootstrap-formhelpers-min.css" media="screen">
        <link rel="stylesheet" href="css/bootstrapValidator-min.css"/>
        <link rel="stylesheet" href="https://netdna.bootstrapcdn.com/font-awesome/4.0.3/css/font-awesome.css" />
        <link rel="stylesheet" href="css/bootstrap-side-notes.css" />
        <style type="text/css">
            .col-centered {
                display:inline-block;
                float:none;
                text-align:left;
                margin-right:-4px;
            }
            .row-centered {
                margin-left: 9px;
                margin-right: 9px;
            } 
        </style>
        <style>
            .blank_row
            {
                height: 10px !important; /* Overwrite any previous rules */
                background-color: #FFFFFF;
            }
            .tiny_skip{
                margin-top: 10px;
                margin-bottom: 10px}
            .small_skip{
                margin-top: 20px;
                margin-bottom: 20px}
            .med_skip{
                margin-top: 40px;
                margin-bottom: 40px}
        </style>
        <script type="text/javascript" src="https://js.stripe.com/v2/"></script>
        <script src="https://ajax.googleapis.com/ajax/libs/jquery/1.11.0/jquery.min.js"></script>
        <script src="js/bootstrap-min.js"></script>
        <script src="js/bootstrap-formhelpers-min.js"></script>
        <script type="text/javascript" src="js/bootstrapValidator-min.js"></script>
        <script type="text/javascript">
            $(document).ready(function() {
                $('#payment-form').bootstrapValidator({
                    message: 'This value is not valid',
                    feedbackIcons: {
                        valid: 'glyphicon glyphicon-ok',
                        invalid: 'glyphicon glyphicon-remove',
                        validating: 'glyphicon glyphicon-refresh'
                    },
                    submitHandler: function(validator, form, submitButton) {
                        // createToken returns immediately - the supplied callback submits the form if there are no errors
                        Stripe.card.createToken({
                            number: $('.card-number').val(),
                            cvc: $('.card-cvc').val(),
                            exp_month: $('.card-expiry-month').val(),
                            exp_year: $('.card-expiry-year').val(),
                            name: $('.card-holder-name').val(),
                            address_line1: $('.address').val(),
                            address_city: $('.city').val(),
                            address_zip: $('.zip').val(),
                            address_state: $('.state').val(),
                            address_country: $('.country').val()
                        }, stripeResponseHandler);
                        return false; // submit from callback
                    },
                    fields: {
                        street: {
                            validators: {
                                notEmpty: {
                                    message: 'The street is required and cannot be empty'
                                },
                                stringLength: {
                                    min: 6,
                                    max: 96,
                                    message: 'The street must be more than 6 and less than 96 characters long'
                                }
                            }
                        },
                        city: {
                            validators: {
                                notEmpty: {
                                    message: 'The city is required and cannot be empty'
                                }
                            }
                        },
                        zip: {
                            validators: {
                                notEmpty: {
                                    message: 'The zip is required and cannot be empty'
                                },
                                stringLength: {
                                    min: 3,
                                    max: 9,
                                    message: 'The zip must be more than 3 and less than 9 characters long'
                                }
                            }
                        },
                        email: {
                            validators: {
                                notEmpty: {
                                    message: 'The email address is required and can\'t be empty'
                                },
                                emailAddress: {
                                    message: 'The input is not a valid email address'
                                },
                                stringLength: {
                                    min: 6,
                                    max: 65,
                                    message: 'The email must be more than 6 and less than 65 characters long'
                                }
                            }
                        },
                        cardholdername: {
                            validators: {
                                notEmpty: {
                                    message: 'The card holder name is required and can\'t be empty'
                                },
                                stringLength: {
                                    min: 6,
                                    max: 70,
                                    message: 'The card holder name must be more than 6 and less than 70 characters long'
                                }
                            }
                        },
                        cardnumber: {
                            selector: '#cardnumber',
                            validators: {
                                notEmpty: {
                                    message: 'The credit card number is required and can\'t be empty'
                                },
                                creditCard: {
                                    message: 'The credit card number is invalid'
                                },
                            }
                        },
                        expMonth: {
                            selector: '[data-stripe="exp-month"]',
                            validators: {
                                notEmpty: {
                                    message: 'The expiration month is required'
                                },
                                digits: {
                                    message: 'The expiration month can contain digits only'
                                },
                                callback: {
                                    message: 'Expired',
                                    callback: function(value, validator) {
                                        value = parseInt(value, 10);
                                        var year         = validator.getFieldElements('expYear').val(),
                                            currentMonth = new Date().getMonth() + 1,
                                            currentYear  = new Date().getFullYear();
                                        if (value < 0 || value > 12) {
                                            return false;
                                        }
                                        if (year == '') {
                                            return true;
                                        }
                                        year = parseInt(year, 10);
                                        if (year > currentYear || (year == currentYear && value > currentMonth)) {
                                            validator.updateStatus('expYear', 'VALID');
                                            return true;
                                        } else {
                                            return false;
                                        }
                                    }
                                }
                            }
                        },
                        expYear: {
                            selector: '[data-stripe="exp-year"]',
                            validators: {
                                notEmpty: {
                                    message: 'The expiration year is required'
                                },
                                digits: {
                                    message: 'The expiration year can contain digits only'
                                },
                                callback: {
                                    message: 'Expired',
                                    callback: function(value, validator) {
                                        value = parseInt(value, 10);
                                        var month        = validator.getFieldElements('expMonth').val(),
                                            currentMonth = new Date().getMonth() + 1,
                                            currentYear  = new Date().getFullYear();
                                        if (value < currentYear || value > currentYear + 100) {
                                            return false;
                                        }
                                        if (month == '') {
                                            return false;
                                        }
                                        month = parseInt(month, 10);
                                        if (value > currentYear || (value == currentYear && month > currentMonth)) {
                                            validator.updateStatus('expMonth', 'VALID');
                                            return true;
                                        } else {
                                            return false;
                                        }
                                    }
                                }
                            }
                        },
                        cvv: {
                            selector: '#cvv',
                            validators: {
                                notEmpty: {
                                    message: 'The cvv is required and can\'t be empty'
                                },
                                cvv: {
                                    message: 'The value is not a valid CVV',
                                    creditCardField: 'cardnumber'
                                }
                            }
                        },
                    }
                });
            });
        </script>
        <script type="text/javascript">
            // this identifies your website in the createToken call below
            Stripe.setPublishableKey("pk_live_ECGpiwgYMJctgvU0AuVViE3x");

            function stripeResponseHandler(status, response) {
                if (response.error) {
                    // re-enable the submit button
                    $('.submit-button').removeAttr("disabled");
                    // show hidden div
                    document.getElementById('a_x200').style.display = 'block';
                    // show the errors on the form
                    $(".payment-errors").html(response.error.message);
                } else {
                    var form$ = $("#payment-form");
                    // token contains id, last4, and card type
                    var token = response['id'];
                    // insert the token into the form so it gets submitted to the server
                    form$.append("<input type='hidden' name='stripeToken' value='" + token + "' />");
                    //form$.append("<input type='hidden' name='country' value='US' />");
                    // and submit
                    form$.get(0).submit();
                }
            }


        </script>
    </head>
    <body>
        <form action="" method="POST" id="payment-form" class="form-horizontal">
            <div class="row">
                <div class="col-md-4" style="text-align:center">
                    <div class="small_skip">
                        <a  href="https://www.icps2017.it/"><img style="width:135px;height:202px;" src="LOGO.jpg" alt="ICPS_logo" hspace="50"></a>
                        <!-- <a  href="https://stripe.com/"><img style="width:114;height:41px;" src="stripe_logo.png" alt="stripe_logo"></a>-->
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="page-header">
                        <h2 class="gdfg">ICPS2017 Fee payment form</h2>
                        Secure transaction powered by <a  href="https://stripe.com/"><img style="width:114;height:41px;" src="stripe_logo.png" alt="stripe_logo"></a>
                    </div>
                    <noscript>
                        <div class="bs-callout bs-callout-danger">
                            <h4>JavaScript is not enabled!</h4>
                            <p>This payment form requires your browser to have JavaScript enabled. Please activate JavaScript and reload this page. Check <a href="http://enable-javascript.com" target="_blank">enable-javascript.com</a> for more informations.</p>
                        </div>
                    </noscript>
                    <?php
                    require 'lib/Stripe.php';

                    $error = '';
                    $success = '';

                    if ($_POST) {
                        Stripe::setApiKey($APIlive);

                        $round = $_GET['round'];

                        // ******************** if NOT LATE registration ---> EARLY
                        if ($round!='late'){
                            try {
                                if (empty($_POST['street']) || empty($_POST['city']) || empty($_POST['zip']))
                                    throw new Exception("Fill out all required fields.");
                                if (!isset($_POST['stripeToken']))
                                    throw new Exception("The Stripe Token was not generated correctly");
                                if ($_POST['paese']=='NEU'){
                                    if ($_POST['individual_membership']=='yes'){
                                        Stripe_Charge::create(array("amount" => 21656,
                                                                    "currency" => "eur",
                                                                    "card" => $_POST['stripeToken'],
                                                                    "description" => $_POST['uid']." ".$_POST['email'],
                                                                    "receipt_email" => $_POST['email']));
                                        $success = '<div class="alert alert-success">
                                                <strong>Success!</strong> Your payment was successful.
				                                </div>';
                                    } elseif ($_POST['individual_membership']=='no'){
                                        Stripe_Charge::create(array("amount" => 20625,
                                                                    "currency" => "eur",
                                                                    "card" => $_POST['stripeToken'],
                                                                    "description" => $_POST['uid']." ".$_POST['email'],
                                                                    "receipt_email" => $_POST['email']));
                                        $success = '<div class="alert alert-success">
                                                <strong>Success!</strong> Your payment was successful.
				                                </div>';
                                    }
                                } else  if ($_POST['paese']=='EU') {
                                    if ($_POST['individual_membership']=='yes'){
                                        Stripe_Charge::create(array("amount" => 21326,
                                                                    "currency" => "eur",
                                                                    "card" => $_POST['stripeToken'],
                                                                    "description" => $_POST['uid']." ".$_POST['email'],
                                                                    "receipt_email" => $_POST['email']));
                                        $success = '<div class="alert alert-success">
                                                <strong>Success!</strong> Your payment was successful.
				                                </div>';
                                    } elseif ($_POST['individual_membership']=='no'){
                                        Stripe_Charge::create(array("amount" => 20311,
                                                                    "currency" => "eur",
                                                                    "card" => $_POST['stripeToken'],
                                                                    "description" => $_POST['uid']." ".$_POST['email'],
                                                                    "receipt_email" => $_POST['email']));
                                        $success = '<div class="alert alert-success">
                                                <strong>Success!</strong> Your payment was successful. 
                                                An email has been sent to your address with the Stripe payment receipt.
				                                </div>';
                                    }
                                } else {
                                    $success = '<div class="alert alert-danger">
                <strong>FAILURE!</strong> Something went wrong.
				</div>';
                                }
                            }
                            catch (Exception $e) {
                                $error = '<div class="alert alert-danger">
			  <strong>Error!</strong> '.$e->getMessage().'
			  </div>';
                            }
                        } 
                        // ******************* if LATE registration
                        else {
                            try {
                                if (empty($_POST['street']) || empty($_POST['city']) || empty($_POST['zip']))
                                    throw new Exception("Fill out all required fields.");
                                if (!isset($_POST['stripeToken']))
                                    throw new Exception("The Stripe Token was not generated correctly");
                                if ($_POST['paese']=='NEU'){
                                    if ($_POST['individual_membership']=='yes'){
                                        Stripe_Charge::create(array("amount" => 24743,
                                                                    "currency" => "eur",
                                                                    "card" => $_POST['stripeToken'],
                                                                    "description" => $_POST['uid']." ".$_POST['email'],
                                                                    "receipt_email" => $_POST['email']));
                                        $success = '<div class="alert alert-success">
                                                <strong>Success!</strong> Your payment was successful.
				                                </div>';
                                    } elseif ($_POST['individual_membership']=='no'){
                                        Stripe_Charge::create(array("amount" => 23713,
                                                                    "currency" => "eur",
                                                                    "card" => $_POST['stripeToken'],
                                                                    "description" => $_POST['uid']." ".$_POST['email'],
                                                                    "receipt_email" => $_POST['email']));
                                        $success = '<div class="alert alert-success">
                                                <strong>Success!</strong> Your payment was successful.
				                                </div>';
                                    }
                                } else  if ($_POST['paese']=='EU') {
                                    if ($_POST['individual_membership']=='yes'){
                                        Stripe_Charge::create(array("amount" => 24367,
                                                                    "currency" => "eur",
                                                                    "card" => $_POST['stripeToken'],
                                                                    "description" => $_POST['uid']." ".$_POST['email'],
                                                                    "receipt_email" => $_POST['email']));
                                        $success = '<div class="alert alert-success">
                                                <strong>Success!</strong> Your payment was successful.
				                                </div>';
                                    } elseif ($_POST['individual_membership']=='no'){
                                        Stripe_Charge::create(array("amount" => 23352,
                                                                    "currency" => "eur",
                                                                    "card" => $_POST['stripeToken'],
                                                                    "description" => $_POST['uid']." ".$_POST['email'],
                                                                    "receipt_email" => $_POST['email']));
                                        $success = '<div class="alert alert-success">
                                                <strong>Success!</strong> Your payment was successful. 
                                                An email has been sent to your address with the Stripe payment receipt.
				                                </div>';
                                    }
                                } else {
                                    $success = '<div class="alert alert-danger">
                <strong>FAILURE!</strong> Something went wrong.
				</div>';
                                }
                            }
                            catch (Exception $e) {
                                $error = '<div class="alert alert-danger">
			  <strong>Error!</strong> '.$e->getMessage().'
			  </div>';
                            }
                        }
                    }

                    ?>
                    <div class="alert alert-danger" id="a_x200" style="display: none;"> <strong>Error!</strong> <span class="payment-errors"></span> </div>
                    <span class="payment-success">
                        <?= $success ?>
                        <?= $error ?>
                    </span>
                    <fieldset>

                        <!-- Form Name -->
                        <legend>Participant</legend>

                        <!-- Street -->
                        <div class="form-group">
                            <label class="col-sm-4 control-label" for="textinput">Name</label>
                            <div class="col-sm-6">
                                <input type="text/css" name="cognome" readonly value="<?php echo $_GET['name'] ?>" class="address form-control"/>
                            </div>
                        </div>
                        <div class="form-group">
                            <label class="col-sm-4 control-label" for="textinput">Surname</label>
                            <div class="col-sm-6">
                                <input type="text" name="nome" readonly value="<?php echo $_GET['surname'] ?>" class="address form-control"/>
                            </div>
                        </div>
                        <div class="form-group">
                            <label class="col-sm-4 control-label" for="textinput">Unique identifier</label>
                            <div class="col-sm-6">
                                <input type="text" name="uid" readonly value="<?php echo $_GET['uid'] ?>" class="address form-control"/>
                            </div>
                        </div>

                        <legend>Items</legend>
                        <div class="form-group">
                            <label class="col-sm-4 control-label" for="textinput"></label>

                            <?php
    $im = $_GET['im'];
                        // ********************* if NOT LATE
                        if ($round!='late'){
                            echo "<div class=\"col-sm-8\">
                                        <table>
                                            <tr>
                                                <td width=\"20\">
                                                    <input type=\"radio\" name=\"base\" value=\"base\" checked> 
                                                </td>
                                                <td align=\"right\">
                                                    <b>190.00€</b>
                                                </td>
                                                <td>&nbsp;&nbsp;</td>
                                                <td width=\"300\" align=\"left\">
                                                    Early Bird fee
                                                </td>
                                            </tr>
                                            <tr>
                                                <td width=\"20\">
                                                    <input type=\"radio\" name=\"exc\" value=\"exec\" checked> 
                                                </td>
                                                <td align=\"right\">
                                                    <b>10.00€</b>
                                                </td>
                                                <td></td>
                                                <td width=\"300\">
                                                    Excursion fee
                                                </td>
                                            </tr>";
                            if ($im=='yes') {
                                echo "
                                        <tr>
                                        <td width=\"30\">
                                            <input type=\"radio\" name=\"iaps\" value=\"iaps\" checked> 
                                        </td>
                                        <td align=\"right\">
                                        <b>10.00€</b>
                                        </td>
                                        <td></td>
                                        <td width=\"300\">
                                            IAPS membership fee
                                        </td>
                                        </tr>
                                        <tr class=\"blank_row\" style=\"border-bottom: 1px solid #000;\">
                                        <td colspan=\"4\"></td>
                                        </tr>
                                        <tr class=\"blank_row\">
                                        <td colspan=\"4\"></td>
                                        </tr>
                                        <tr>
                                        <td></td>
                                        <td align=\"right\"><b>210.00€</b></td> 
                                        <td></td>
                                        <td width=\"300\">Net total</td>
                                        </tr>
                                        ";
                            } else 
                            {
                                echo "
                                        <tr class=\"blank_row\" style=\"border-bottom: 1px solid #000;\">
                                        <td colspan=\"4\"></td>
                                        </tr>
                                        <tr class=\"blank_row\">
                                        <td colspan=\"4\"></td>
                                        </tr>
                                        <tr>
                                        <td></td>
                                        <td align=\"right\"><b>200.00€</b></td> 
                                        <td></td>
                                        <td width=\"300\">Net total</td>
                                        </tr>
                                        ";
                            }                 
                            echo "</table>
                                    </div>
                                    </div>";
                        } 
                        // ****************** if LATE
                        else {
                            echo "<div class=\"col-sm-8\">
                                        <table>
                                            <tr>
                                                <td width=\"20\">
                                                    <input type=\"radio\" name=\"base\" value=\"base\" checked> 
                                                </td>
                                                <td align=\"right\">
                                                    <b>220.00€</b>
                                                </td>
                                                <td>&nbsp;&nbsp;</td>
                                                <td width=\"300\" align=\"left\">
                                                    Late registration fee
                                                </td>
                                            </tr>
                                            <tr>
                                                <td width=\"20\">
                                                    <input type=\"radio\" name=\"exc\" value=\"exec\" checked> 
                                                </td>
                                                <td align=\"right\">
                                                    <b>10.00€</b>
                                                </td>
                                                <td></td>
                                                <td width=\"300\">
                                                    Excursion fee
                                                </td>
                                            </tr>";
                            if ($im=='yes') {
                                echo "
                                        <tr>
                                        <td width=\"30\">
                                            <input type=\"radio\" name=\"iaps\" value=\"iaps\" checked> 
                                        </td>
                                        <td align=\"right\">
                                        <b>10.00€</b>
                                        </td>
                                        <td></td>
                                        <td width=\"300\">
                                            IAPS membership fee
                                        </td>
                                        </tr>
                                        <tr class=\"blank_row\" style=\"border-bottom: 1px solid #000;\">
                                        <td colspan=\"4\"></td>
                                        </tr>
                                        <tr class=\"blank_row\">
                                        <td colspan=\"4\"></td>
                                        </tr>
                                        <tr>
                                        <td></td>
                                        <td align=\"right\"><b>240.00€</b></td> 
                                        <td></td>
                                        <td width=\"300\">Net total</td>
                                        </tr>
                                        ";
                            } else 
                            {
                                echo "
                                        <tr class=\"blank_row\" style=\"border-bottom: 1px solid #000;\">
                                        <td colspan=\"4\"></td>
                                        </tr>
                                        <tr class=\"blank_row\">
                                        <td colspan=\"4\"></td>
                                        </tr>
                                        <tr>
                                        <td></td>
                                        <td align=\"right\"><b>230.00€</b></td> 
                                        <td></td>
                                        <td width=\"300\">Net total</td>
                                        </tr>
                                        ";
                            }                 
                            echo "</table>
                                    </div>";
                        } 
                            ?>
                        </div>
                        <legend>Billing Details</legend>

                        <!-- Street -->
                        <div class="form-group">
                            <label class="col-sm-4 control-label" for="textinput">Street</label>
                            <div class="col-sm-6">
                                <input type="text" name="street" placeholder="Street" class="address form-control">
                            </div>
                        </div>

                        <!-- City -->
                        <div class="form-group">
                            <label class="col-sm-4 control-label" for="textinput">City</label>
                            <div class="col-sm-6">
                                <input type="text" name="city" placeholder="City" class="city form-control">
                            </div>
                        </div>

                        <!-- State -->
                        <div class="form-group">
                            <label class="col-sm-4 control-label" for="textinput">State</label>
                            <div class="col-sm-6">
                                <input type="text" name="state" maxlength="65" placeholder="State" class="state form-control">
                            </div>
                        </div>

                        <!-- Postcal Code -->
                        <div class="form-group">
                            <label class="col-sm-4 control-label" for="textinput">Postal Code</label>
                            <div class="col-sm-6">
                                <input type="text" name="zip" maxlength="9" placeholder="Postal Code" class="zip form-control">
                            </div>
                        </div>

                        <!-- Country -->
                        <div class="form-group">
                            <label class="col-sm-4 control-label" for="textinput">Country</label>
                            <div class="col-sm-6"> 
                                <!--input type="text" name="country" placeholder="Country" class="country form-control"-->
                                <div class="country bfh-selectbox bfh-countries" name="country" placeholder="Select Country" data-flags="true" data-filter="true"> </div>
                            </div>
                        </div>

                        <!-- Email -->
                        <div class="form-group">
                            <label class="col-sm-4 control-label" for="textinput">Email</label>
                            <div class="col-sm-6">
                                <input type="text" name="email" maxlength="65" placeholder="Email" class="email form-control">
                            </div>
                        </div>
                    </fieldset>
                    <fieldset>
                        <legend>Card Details</legend>

                        <!-- Card Holder Name -->
                        <div class="form-group">
                            <label class="col-sm-4 control-label"  for="textinput">Card Holder's Name</label>
                            <div class="col-sm-6">
                                <input type="text" name="cardholdername" maxlength="70" placeholder="Card Holder Name" class="card-holder-name form-control">
                            </div>
                        </div>

                        <!-- Card Number -->
                        <div class="form-group">
                            <label class="col-sm-4 control-label" for="textinput">Card Number</label>
                            <div class="col-sm-6">
                                <input type="text" id="cardnumber" maxlength="19" placeholder="Card Number" class="card-number form-control">
                                <!--<input type='hidden' name='country' value='US' />-->
                            </div>
                        </div>
                        <!-- $('#cardnumber').val().substr(0,6)-->
                        <script>
                            $('#cardnumber').on('change', function() {               
                                var country="";
                                var cardnum = $('#cardnumber').val().replace(/\s+/g, '');
                                var culo = cardnum.substr(0,6);
                                var base_url = "https://lookup.binlist.net/";
                                var str = base_url.concat(culo);
                                //$('#quota').html(str);
                                //$('#quota').html(culo);
                                $.get(str,function(data){
                                    var country = data["country"]['alpha2'];
                                    var countries = ["AD","AT","BE","BG","HR","CY","CZ","DK","EE","FO","FI","FR","DE","GI","GR","VA",
                                                     "HU","IS","IE","IT","LV","LI","LT","LU","MT","NL","NO","PL","PT","PM","RO","SM","SK",
                                                     "SI","ES","SE","CH","GB"];
                                    //$('#quota').html(country);
                                    if ($round!='late'){
                                        if (countries.indexOf(country)>-1){
                                            $('#paese').val('EU');
                                            var im = "<?php echo $im; ?>";
                                            //$('#fee').html(im);
                                            if (im=='yes'){
                                                $('#fee').html('3.26€');
                                                $('#total').html('213.26€');
                                            } else {
                                                $('#fee').html('3.11€');
                                                $('#total').html('203.11€');
                                            }
                                        } else{
                                            $('#paese').val('NEU');
                                            var im = "<?php echo $im; ?>";
                                            //$('#fee').html(im);
                                            if (im=='yes'){
                                                $('#fee').html('6.56€');
                                                $('#total').html('216.56€');
                                            } else {
                                                $('#fee').html('6.25€');
                                                $('#total').html('206.25€');
                                            }
                                        }
                                    } else {
                                        if (countries.indexOf(country)>-1){
                                            $('#paese').val('EU');
                                            var im = "<?php echo $im; ?>";
                                            //$('#fee').html(im);
                                            if (im=='yes'){
                                                $('#fee').html('3.67€');
                                                $('#total').html('243.67€');
                                            } else {
                                                $('#fee').html('3.52€');
                                                $('#total').html('233.52€');
                                            }
                                        } else{
                                            $('#paese').val('NEU');
                                            var im = "<?php echo $im; ?>";
                                            //$('#fee').html(im);
                                            if (im=='yes'){
                                                $('#fee').html('7.43€');
                                                $('#total').html('247.43€');
                                            } else {
                                                $('#fee').html('7.13€');
                                                $('#total').html('237.13€');
                                            }
                                        }
                                    }
                                    //$("#payment-form").append("<input type='hidden' name='country' value='US' />");
                                });               
                            });
                        </script>

                        <!-- Expiry-->
                        <div class="form-group">
                            <label class="col-sm-4 control-label" for="textinput">Card Expiry Date</label>
                            <div class="col-sm-6">
                                <div class="form-inline">
                                    <select name="select2" data-stripe="exp-month" class="card-expiry-month stripe-sensitive required form-control">
                                        <option value="01" selected="selected">01</option>
                                        <option value="02">02</option>
                                        <option value="03">03</option>
                                        <option value="04">04</option>
                                        <option value="05">05</option>
                                        <option value="06">06</option>
                                        <option value="07">07</option>
                                        <option value="08">08</option>
                                        <option value="09">09</option>
                                        <option value="10">10</option>
                                        <option value="11">11</option>
                                        <option value="12">12</option>
                                    </select>
                                    <span> / </span>
                                    <select name="select2" data-stripe="exp-year" class="card-expiry-year stripe-sensitive required form-control">
                                    </select>
                                    <script type="text/javascript">
                                        var select = $(".card-expiry-year"),
                                            year = new Date().getFullYear();

                                        for (var i = 0; i < 12; i++) {
                                            select.append($("<option value='"+(i + year)+"' "+(i === 0 ? "selected" : "")+">"+(i + year)+"</option>"))
                                        }
                                    </script> 
                                </div>
                            </div>
                        </div>

                        <!-- CVV -->
                        <div class="form-group">
                            <label class="col-sm-4 control-label" for="textinput">CVV/CVV2</label>
                            <div class="col-sm-3">
                                <input type="text" id="cvv" placeholder="CVV" maxlength="4" class="card-cvc form-control">
                            </div>
                        </div>

                        <!--  <legend>Amount to pay</legend>-->
                        <div class="form-group">
                            <label class="col-sm-4 control-label" for="textinput"></label>
                            <!-- <div class="col-sm-8">-->
                            <div class="panel panel-success">
                                <div class="panel-heading"> 
                                    <h3 class="panel-title">Amount to pay</h3>
                                </div>
                                <div class="panel-body" align="center">
                                    <table>
                                        <tr>
                                            <td align="right">
                                                <span style="font-size: 12pt">
                                                    <?php
                                                    if ($im=='yes'){
                                                        echo "
                                                    210.00€
                                                ";
                                                    } else {
                                                        echo "
                                                    200.00€
                                                ";
                                                    }
                                                    ?>
                                                </span>
                                            </td>
                                            <td>&nbsp;&nbsp;&nbsp;</td>
                                            <td align="left">
                                                <span style="font-size: 12pt">
                                                    Net total
                                                </span>
                                            </td>
                                        </tr>
                                        <tr>
                                            <td align="right">
                                                <span id="fee" style="font-size: 12pt"></span>    
                                            </td>
                                            <td>&nbsp;&nbsp;&nbsp;</td>
                                            <td align="left">
                                                <span style="font-size: 12pt">
                                                    <a href="https://stripe.com/it/pricing">Stripe</a> fees    
                                                </span>
                                            </td>
                                        </tr>
                                        <tr class="blank_row" style="border-bottom: 1px solid #000;">
                                            <td colspan="4"></td>
                                        </tr>
                                        <tr class="blank_row">
                                            <td colspan="4"></td>
                                        </tr>
                                        <tr>
                                            <td align="right">
                                                <span id="total" style="font-weight: bold; font-size: 14pt"></span>    
                                            </td>
                                            <td>&nbsp;&nbsp;&nbsp;</td>
                                            <td align="left">
                                                <span style="font-size: 14pt">Total to pay</span>
                                            </td>
                                        </tr>
                                    </table>
                                </div>
                            </div>
                        </div>




                        <div class="form-group">
                            <div id="quota" class="col-sm-6"></div>
                            <input type='hidden' name='paese' id='paese' value='EU' />
                            <?php
                            echo "<input type=\"hidden\" name=\"individual_membership\" id=\"individual_membership\" value=\"".$im."\"/>";
                            ?>
                        </div>
                        <!-- <input type='hidden' name='country' value='' />-->


                        <!-- Important notice -->
                        <div class="form-group">
                            <!--       <div class="panel panel-success">
<div class="panel-heading">
<h3 class="panel-title">Important notice</h3>
</div>
<div class="panel-body">
<p>Your card will be charged 30€ after submit.</p>
<p>Your account statement will show the following booking text:
XXXXXXX </p>
</div>
</div>-->

                            <!-- Submit -->
                            <div class="control-group">
                                <div class="controls">
                                    <center>
                                        <button class="btn btn-success" type="submit">Pay Now</button>
                                    </center>
                                </div>
                            </div>
                        </div>
                    </fieldset>
                </div>
                <!--<div class="col-md-4" style="text-align:left">
<div class="small_skip"></div>
<a  href="https://www.icps2017.it/"><img style="width:135px;height:202px;" src="LOGO.jpg" alt="ICPS_logo"></a>
</div>-->
            </div>
        </form>
    </body>
</html>
