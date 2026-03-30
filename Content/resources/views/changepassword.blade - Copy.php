@extends('master')
@section('content')


<script>
var myInput = document.getElementById("fnew");
var letter = document.getElementById("letter");
var capital = document.getElementById("capital");
var number = document.getElementById("number");
var charcter = document.getElementById("charcter");
var length = document.getElementById("length");

// When the user clicks on the password field, show the message box
myInput.onfocus = function() {
  document.getElementById("message").style.display = "block";
}

// When the user clicks outside of the password field, hide the message box
myInput.onblur = function() {
  document.getElementById("message").style.display = "none";
}

// When the user starts to type something inside the password field
myInput.onkeyup = function() {
  // Validate lowercase letters
  var lowerCaseLetters = /[a-z]/g;
  if(myInput.value.match(lowerCaseLetters)) {
    letter.classList.remove("invalid");
    letter.classList.add("valid");
  } else {
    letter.classList.remove("valid");
    letter.classList.add("invalid");
}

  // Validate capital letters
  var upperCaseLetters = /[A-Z]/g;
  if(myInput.value.match(upperCaseLetters)) {
    capital.classList.remove("invalid");
    capital.classList.add("valid");
  } else {
    capital.classList.remove("valid");
    capital.classList.add("invalid");
  }

  // Validate numbers
  var numbers = /[0-9]/g;
  if(myInput.value.match(numbers)) {
    number.classList.remove("invalid");
    number.classList.add("valid");
  } else {
    number.classList.remove("valid");
    number.classList.add("invalid");
  }

  // Validate length
  if(myInput.value.length >= 8) {
    length.classList.remove("invalid");
    length.classList.add("valid");
  } else {
    length.classList.remove("valid");
    length.classList.add("invalid");
  }


  var charcterformat = /[ `!@#$%^&*()_+\-=\[\]{};':"\\|,.<>\/?~]/;
  if(myInput.value.match(charcterformat)) {
    charcter.classList.remove("invalid");
    charcter.classList.add("valid");
  } else {
    charcter.classList.remove("valid");
    charcter.classList.add("invalid");
  }

}


function validatePassword(password) {
        // Regex pattern to check the conditions
        const pattern = /^(?=.*[0-9])(?=.*[!@#$%^&*])(?=.*[a-z])(?=.*[A-Z]).{8,}$/;
        
        // Test the password against the pattern
        if (pattern.test(password)) {
            return true;
        } else {
            return false;
        }
    }


function validateForm() {
    var sites = {!! json_encode($userpass) !!};  
  let pcur = document.forms["my-form"]["fcur"].value;
  let pnew = document.forms["my-form"]["fnew"].value;
  let pconf = document.forms["my-form"]["fconf"].value;


  if (!validatePassword(pnew) || !validatePassword(pconf)) {
            alert("Password must contain at least one number, one special character, one uppercase letter, one lowercase letter, and be at least 8 characters long.");
            return false;
        }else{

          if(pnew == pconf){
              return true;
          }else{
              alert("New Passwords are not same.");
              return false; 
          }
        
  }

  
}
</script>

<style>
input {
  width: 100%;
  padding: 12px;
  border: 1px solid #ccc;
  border-radius: 4px;
  box-sizing: border-box;
  margin-top: 6px;
  margin-bottom: 16px;
}

/* Style the submit button */
input[type=submit] {
  background-color: #04AA6D;
  color: white;
}

/* Style the container for inputs */
.container {
  background-color: #f1f1f1;
  padding: 20px;
}

/* The message box is shown when the user clicks on the password field */
#message {
  display:none;
  background: #f1f1f1;
  color: #000;
  position: relative;
  padding: 20px;
  margin-top: 10px;
}

#message p {
  padding: 10px 35px;
  font-size: 18px;
}

/* Add a green text color and a checkmark when the requirements are right */
.valid {
  color: green;
}

.valid:before {
  position: relative;
  left: -35px;
  content: "&#10004;";
}

/* Add a red text color and an "x" icon when the requirements are wrong */
.invalid {
  color: red;
}

.invalid:before {
  position: relative;
  left: -35px;
  content: "&#10006;";
}
</style>

<div class="content-body">
<div class="container-fluid">
<div class="row">
<div class="col-xl-9 col-xxl-12">


<div class="card">
<div class="card-header">
<h4 class="card-title">Change Password</h4>
</div>
<div class="card-body">
<div class="basic-form">
<form class="form-horizontal" role="form" method="POST" id="my-form" onsubmit="return validateForm()" action="{{ url('changePassword') }}" name="my-form" autocomplete="off">
                        {!! csrf_field() !!}



<div class="form-row">
<div class="form-group col-md-4">
<label>Current Password</label>
<input type="password" class="form-control" placeholder="Current Password" name="fcur" required>
</div>
</div>


<div >
  <p style="color:#FF0000">Password must contain the following: Minimum 8 characters, 1 lowercase letter, 1 Uppercase letter, 1 number, 1 Special character</p>
</div>

<div class="form-row">
    <div class="form-group col-md-4">
        <label>New Password</label>
        <input type="password" class="form-control" name="fnew" placeholder="New Password" required
            pattern="^(?=.*[0-9])(?=.*[!@#$%^&*+`~'=?\|[\](){}<>/\-])(?=.*[a-z])(?=.*[A-Z]).{8,}$"
            title="Password must contain at least one number, one special character, one uppercase letter, one lowercase letter, and be at least 8 characters long.">
    </div>

    <div class="form-group col-md-4">
        <label>Confirm Password</label>
        <input type="password" class="form-control" placeholder="Confirm Password" required 
            pattern="^(?=.*\d)(?=.*[a-z])(?=.*[A-Z])(?=.*?[!@#$%^&*+`~'=?\|\]\[\(\)\-<>/]).{8,}$" 
            title="Must contain at least one number and one special character and one uppercase and lowercase letter, and at least 8 or more characters" 
            name="fconf" id="fconf">
    </div>
</div>



<button type="submit" class="btn btn-primary">Submit</button>
</form>
</div>
</div>
</div>
</div>


</div>
</div>
</div>
</div>


@endsection