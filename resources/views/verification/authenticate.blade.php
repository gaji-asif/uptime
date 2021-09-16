<!DOCTYPE html>
<html lang="en">
  <head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <meta name="description" content="">
    <meta name="author" content="Mark Otto, Jacob Thornton, and Bootstrap contributors">
    <meta name="generator" content="Jekyll v3.8.5">
    <title>UptimeProfile - Verify Submission</title>

    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.3.1/css/bootstrap.min.css" integrity="sha384-ggOyR0iXCbMQv3Xipma34MD+dH/1fQ784/j6cY/iJTQUOhcWr7x9JvoRxT2MZw1T" crossorigin="anonymous">
    
    <!-- Custom styles for this template -->
    <link href="{{asset('css/verification.css')}}" rel="stylesheet">

    <link rel="stylesheet" href="{{asset('css/intlTelInput.css')}}">
  </head>
  <body class="text-center">
    <div class="cover-container d-flex w-100 h-100 p-3 mx-auto flex-column">
  <header class="masthead mb-auto">
    <div class="inner">

      <h6 class="masthead-brand">
        <div>
          <img src="{{asset('images/uptime-logo.png')}}" alt=""><br>
          Your Uptime Verified
          <br>
          <br>
        </div>
      </h6>
    </div>
  </header>

  <main role="main" class="inner cover">
    <div class="container content text-left">
      <div class="row justify-content-center">
        <div class="col-lg-6 col-md-6 col-sm-12 bgphoto" style="display: block">
          <div class="photo" style="background-image: url(<?php echo $preview_image ?>);background-size: 100% 100%;">
          </div>
        </div>
        <div class="col-lg-4 col-md-6 col-sm-12 sidebar">
          <br>
          <div class="alert alert-info" role="alert">
            <i class="fa fa-warning" style="color:#33C589;font-size: 25px;vertical-align: middle;"></i>
            Please verify your identity
          </div>
          <div class="card ml-auto mr-auto" >
            <div class="card-body">
              <form id="id_form" name="identification" action="{{ $nextStep }}" method="POST">
                @csrf
                <div class="form-group">
                  <label for="firstname">First Name</label>
                  <input type="text" class="form-control" id="firstname"
                    value="{{$verifier['firstname']}}" id="firstname" name="firstname" 
                    <?php echo (!!$authStep)?"disabled":"" ?> required>
                </div>
                <div class="form-group">
                  <label for="lastname">Last Name</label>
                  <input type="text" class="form-control" name="lastname" 
                    value="{{$verifier['lastname']}}" id="lastname" name="lastname" 
                    <?php echo (!!$authStep)?"disabled":"" ?> required>
                </div>
                
                <div class="form-group">
                  <label for="phonenumber">Phone number</label>
                  <input type="text" class="form-control" id="phonenumber" maxlength="20"
                    value="{{$verifier['phonenumber']}}" id="phonenumber" name="phonenumber" 
                    <?php echo (!!$authStep)?"disabled":"" ?> required>
                </div>

                @if ($authStep == 1)
                <div class="form-group">
                  <label for="verifycode">Verification Code</label>
                  <input type="text" class="form-control" id="verifycode" name="verifycode">
                </div>
                @endif

                <input type="hidden" name="verificationId" id="verificationId" value="{{ $verificationId }}">
                <input type="hidden" name="idToken" id="idToken">

                @if ($authStep == 1)
                  <button type="button" class="btn btn-primary btn-block" id="confirm-button">
                    <span class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span>
                    Confirm
                  </button>
                @else
                  <button type="button" class="btn btn-primary btn-block" id="sign-in-button" disabled="disabled">
                    <span class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span>
                    Send Verification
                  </button>
                @endif
              </form>
            </div>
          </div>

          @if($errors->any())
          <p class="text-center">
            <h6 style="color: red">{{$errors->first()}}</h6>
          </p>
          @endif
          
        </div>
      </div>
    </div>
  </main>

  <footer class="mastfoot mt-auto">
    <div class="inner">
      <br>
      <p>&copy; {{ date("Y") }} by <a href="https://uptimeprofile.com/">UptimeProfile.com</a>.</p>
    </div>
  </footer>


</div>

<!-- Modal -->
<div class="modal fade text-left" id="disclaimerModel" tabindex="-1" role="dialog" aria-labelledby="disclaimerModelTitle" aria-hidden="true">
  <div class="modal-dialog modal-dialog-centered" role="document">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title" id="exampleModalLongTitle">Disclaimer</h5>
      </div>
      <div class="modal-body">
        <h3 class="text-center">Terms &amp; Conditions</h3>
        <p>
          By continuing verification of {{ $employee->full_name }}'s photo submissions
          you are agreeing that they are valid and true. Your E-signature will be placed
          on each image if approved by you. Your first, last name and contact number
          will appear on their resume as a verifed client. We will hide your personal
          number through phone anonymisation for your security, allowing others to
          contact you if needed.
        </p>
        <br>
        <div class="form-check">
          <input class="form-check-input" type="checkbox" value="" id="agreecheck">
          <label class="form-check-label" for="agreecheck">
            <strong>I agree to the terms stated in this disclosure</strong>
          </label>
        </div>
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-primary" data-dismiss="modal" id="agree-btn" disabled="disabled">Accept</button>
      </div>
    </div>
  </div>
</div>

<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.7.0/css/font-awesome.min.css">
<script
  src="https://code.jquery.com/jquery-3.3.1.min.js"
  integrity="sha256-FgpCb/KJQlLNfOu91ta32o/NMZxltwRo8QtmkMRdAu8="
  crossorigin="anonymous"></script>
<script src="https://stackpath.bootstrapcdn.com/bootstrap/4.3.1/js/bootstrap.min.js" integrity="sha384-JjSmVgyd0p3pXB1rRibZUAYoIIy6OrQ6VrjIEaFf/nJGzIxFDsf4x0xIM+B07jRM" crossorigin="anonymous"></script>
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@8"></script>

  <!-- The core Firebase JS SDK is always required and must be listed first -->
<script src="https://www.gstatic.com/firebasejs/7.1.0/firebase-app.js"></script>
<script src="https://www.gstatic.com/firebasejs/7.1.0/firebase-auth.js"></script>
<script src="https://www.gstatic.com/firebasejs/7.1.0/firebase-database.js"></script>

<script src="{{asset('js/intlTelInput.js')}}"></script>
<script src="{{asset('js/utils.js')}}"></script>


<script>
// Your web app's Firebase configuration
var firebaseConfig = {!! $firebaseConfig !!} ;
// Initialize Firebase
firebase.initializeApp(firebaseConfig);

window.phoneElm = null;

$(function() {
  @if ($authStep != 1)
  $('#disclaimerModel').modal({
    backdrop: 'static'
  });

  $('#agreecheck').change
  $("#agreecheck").change(function() {
      if(this.checked) {
        $('#agree-btn').removeAttr('disabled');
      } else {
        $('#agree-btn').attr('disabled', true);
      }
  });


  // IntlTelInput
  window.phoneElm = window.intlTelInput(document.querySelector("#phonenumber"), {
    autoHideDialCode: false,
    initialCountry: "auto",
    nationalMode: true,
    separateDialCode: true,
    preferredCountries: ["us", "gb", "cn"],
    

    geoIpLookup: function(success, failure) {
      $.get("https://freegeoip.app/json/", function() {}, "json").always(function(resp) {
        var countryCode = (resp && resp.country_code) ? resp.country_code : "";
        success(countryCode);
      });
    },
  });

  $('#phonenumber').on('keypress', function(e){
    var character = String.fromCharCode(event.keyCode);
    return !(/[^0-9 \-\+]/.test(character));
  });

  $('#phonenumber').on('paste', function(e) {
    setTimeout(function() {
      if (/[^0-9 \-\+]/g.test(e.target.value)) {
        e.target.value = e.target.value.replace(/[^0-9 \-\+]/g, '');
      }
    },1);
  });

  firebase.auth().onAuthStateChanged(function(user) {
    // console.log(user);
    if (user) {
      firebase.auth().signOut().then(function() {
        console.log('Signed Out');
      }, function(error) {
        console.error('Sign Out Error', error);
      });
    }
  });
  // [START appVerifier]
  window.recaptchaVerifier = new firebase.auth.RecaptchaVerifier('sign-in-button', {
    'size': 'invisible',
    'callback': function(response) {
      // reCAPTCHA solved, allow signInWithPhoneNumber.
      onSignInSubmit();
    }
  });
  // [END appVerifier]
  
  window.recaptchaVerifier.render().then(function(widgetId) {
    window.recaptchaWidgetId = widgetId;
    setTimeout(function() {
      $('#confirm-button').removeAttr('disabled');
    }, 2000);
  });
  @endif

  $('#confirm-button').click(function() {
    var credential = firebase.auth.PhoneAuthProvider.credential(
      $('#verificationId').val(), $('#verifycode').val()
    );
    $('#confirm-button').attr('disabled', true);

    firebase.auth().signInWithCredential(credential).then(function (result) {
      // User signed in successfully.
      firebase.auth().currentUser.getIdToken().then(function(idToken) {
        $('#idToken').val(idToken);
        $('#id_form').submit();
      }).catch(function(error) {
        console.log(error);
        Swal.fire({
          title: 'Error!',
          text: 'Error while getting Id Token for phone number!',
          type: 'error',
          confirmButtonText: 'OK'
        });
      });
    }).catch(function (error) {
      $('#confirm-button').attr('disabled', false);
      // User couldn't sign in (bad verification code?)
      console.error('Error while checking the verification code', error);
      Swal.fire({
        title: 'Error!',
        text: 'Incorrect verification code!',
        type: 'error',
        confirmButtonText: 'OK'
      });
    });

  });
});

function onSignInSubmit() {
  var phoneNumber = window.phoneElm.getNumber(intlTelInputUtils.numberFormat.E164);
  var appVerifier = window.recaptchaVerifier;
  
  $('#sign-in-button').attr('disabled', true);

  firebase.auth().signInWithPhoneNumber(phoneNumber, appVerifier).then(function (confirmationResult) {
    $('#verificationId').val(confirmationResult.verificationId);
    $('#id_form').submit();
  }).catch(function (error) {
    $('#sign-in-button').attr('disabled', false);
    // Error; SMS not sent
    console.error('Error during signInWithPhoneNumber', error);
    Swal.fire({
      title: 'Error!',
      text: 'Cannot send verification SMS, please check your input phone number!',
      type: 'error',
      confirmButtonText: 'OK'
    });
  });
}

</script>

</body>
</html>
