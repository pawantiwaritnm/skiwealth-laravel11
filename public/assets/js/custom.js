// jQuery('#frmSignin').on('submit',function(e){
// 	jQuery('.field_error').html('');
// 	jQuery('#signin_btn').html('Please wait...');
// 	jQuery('#signin_btn').attr('disabled',true);
// 	jQuery.ajax({
// 		url:SITE_URL+'checkSignIn',
// 		type:'post',
// 		data:jQuery('#frmSignin').serialize(),
// 		success:function(result){
// 			var isError = result.indexOf("elementError");
// 			if(isError>0){
// 				var getData=$.parseJSON(result);
// 				$.each(getData, function(val, key) {
// 					$.each(key, function(fieldVal, fieldKey) {
// 						$.each(fieldKey, function(fieldVal1, fieldKey1) {
// 							if(fieldKey1=='OTP_VERIFY'){
// 								jQuery('#name').attr('disabled','true');
// 								jQuery('#mobile').attr('disabled','true');
// 								jQuery('#email').attr('disabled','true');
// 								jQuery('.otp_box').show();
// 								jQuery('#bottom-wizard').hide();
// 								$("#"+fieldVal1).html(fieldKey1);
// 							}
// 						});
// 					});
// 				});
// 			}
// 			jQuery('#signin_btn').html('Sign In');
// 			jQuery('#signin_btn').attr('disabled',false);
// 		}
// 	});
// 	e.preventDefault();
// });
// $.validator.prototype.errorsFor = function (element) {
//   var name = this.idOrName(element);
//   var elementParent = element.parentElement;
//   return this.errors().filter(function() {
//       return $(this).attr('for') == name && $(this).parent().is(elementParent);
//   });
// }
function onlyNumberKey(evt) {
  var asciiCode = (evt.which)?evt.which: evt.keyCode;
  if(asciiCode >47 && asciiCode < 58)
      return true;
  return false;    
}

jQuery.validator.addMethod(
  "lettersonly",
  function (value, element) {
    return this.optional(element) || /^[a-z\s]+$/i.test(value);
  },
  "Enter only alphabetical characters"
);

jQuery.validator.addMethod(
  "alphanumeric",
  function (value, element) {
    return this.optional(element) || /^[a-z_0-9]+$/i.test(value);
  },
  "Enter only alpha numeric characters."
);

// jQuery.validator.addMethod("alphanumeric", function(value, element) {
// 		return this.optional(element) || /^[\w.]+$/i.test(value);
// 	}, "Letters And Numbers only please");
$(function(){
  $(`input[name="nominee_minor"]`).trigger("change");
});

$("#frmSignin").validate({
  rules: {
    name: {
      required: true,
      lettersonly: true,
      maxlength: 48,
    },
    email: {
      required: true,email: true
    },
    mobile: {
      required: true,
      number: true,
      maxlength: 10,
      minlength: 10,
    },
  },
  messages:{
    email: {
      pattern:'Please enter a valid email address.',
    }
  },
  errorElement: "span",
  errorPlacement: function (error, element) {
    element.closest("li").append(error);
  },
  submitHandler: function (form) {
    jQuery(".field_error").html("");
    jQuery("#signin_btn").html("Please wait...");
    jQuery("#signin_btn").attr("disabled", true);
    jQuery.ajax({
      url: SITE_URL + "checkSignIn",
      type: "post",
      data: jQuery("#frmSignin").serialize(),
      success: function (result) {
        var isError = result.indexOf("elementError");
        if (isError > 0) {
          var getData = $.parseJSON(result);
          $.each(getData, function (val, key) {
            $.each(key, function (fieldVal, fieldKey) {
              $.each(fieldKey, function (fieldVal1, fieldKey1) {
                if (fieldKey1 == "OTP_VERIFY") {
                  jQuery("#name").attr("disabled", "true");
                  jQuery("#mobile").attr("disabled", "true");
                  jQuery("#email").attr("disabled", "true");
                  jQuery(".otp_box").show();
                  jQuery("#bottom-wizard").hide();
                  $("#" + fieldVal1).html(fieldKey1);
                }
              });
            });
          });
        }
        jQuery("#signin_btn").html("Sign In");
        jQuery("#signin_btn").attr("disabled", false);
      },
    });
  },
});

function otpFRM(evt) {
  if(evt.keyCode == 13)
    // $("#signInOtp").submit();
    verify_otp();
}  
function verify_otp() {
  jQuery(".field_error").html("");
  jQuery("#otp_btn").html("Please wait...");
  jQuery("#otp_btn").attr("disabled", true);
  var otp = jQuery("#otp").val();
  jQuery.ajax({
    url: SITE_URL + "verifyOTP",
    type: "post",
    data: "otp=" + otp,
    success: function (result) {
      var isError = result.indexOf("elementError");
      if (isError > 0) {
        var getData = $.parseJSON(result);
        $.each(getData, function (val, key) {
          $.each(key, function (fieldVal, fieldKey) {
            $.each(fieldKey, function (fieldVal1, fieldKey1) {
              if (fieldKey1 == "CORRECT_OTP") {
                window.location.href = SITE_URL + "form";
              }
              if (fieldKey1 == "OTP_ERROR") {
                jQuery("#otp_error").html("Please enter correct OTP");
              }
            });
          });
        });
      }
      jQuery("#otp_btn").html("Verify OTP ");
      jQuery("#otp_btn").attr("disabled", false);
    },
  });
}
jQuery("#signInOtp").on("submit", function (e) {  
  e.preventDefault();

  jQuery(".field_error").html("");
  jQuery("#otp_btn").html("Please wait...");
  jQuery("#otp_btn").attr("disabled", true);
  var otp = jQuery("#otp").val();
  jQuery.ajax({
    url: SITE_URL + "verifyOTP",
    type: "post",
    data: "otp=" + otp,
    success: function (result) {
      var isError = result.indexOf("elementError");
      if (isError > 0) {
        var getData = $.parseJSON(result);
        $.each(getData, function (val, key) {
          $.each(key, function (fieldVal, fieldKey) {
            $.each(fieldKey, function (fieldVal1, fieldKey1) {
              if (fieldKey1 == "CORRECT_OTP") {
                window.location.href = SITE_URL + "form";
              }
              if (fieldKey1 == "OTP_ERROR") {
                jQuery("#otp_error").html("Please enter correct OTP");
              }
            });
          });
        });
      }
      jQuery("#otp_btn").html("Verify OTP ");
      jQuery("#otp_btn").attr("disabled", false);
    },
  });
});  

jQuery("#frmpgelogin").on("submit", function (e) {
  jQuery(".field_error").html("");
  jQuery("#signin_btn").html("Please wait...");
  jQuery("#signin_btn").attr("disabled", true);
  jQuery.ajax({
    url: SITE_URL + "userLogIn",
    type: "post",
    data: jQuery("#frmpgelogin").serialize(),
    success: function (result) { 
      var isError = result.indexOf("elementError");
      if (isError > 0) {
        var getData = $.parseJSON(result);
        $.each(getData, function (val, key) {
          $.each(key, function (fieldVal, fieldKey) {
            $.each(fieldKey, function (fieldVal1, fieldKey1) {
              if (fieldKey1 == "OTP_VERIFY") {
                jQuery("#mobile").attr("disabled", "true");
                jQuery(".otp_box").show();
                jQuery("#bottom-wizard").hide();
                $("#" + fieldVal1).html(fieldKey1);
              } else {
                $("#" + fieldVal1).html(fieldKey1);
              }
            });
          });
        });
      }
      jQuery("#signin_btn").html("LOGIN");
      jQuery("#signin_btn").attr("disabled", false);
    },
  });
  e.preventDefault();
});

$("#check_user").validate({
  rules: {
    mobile: {
      required: true,
      number: true,
      maxlength: 10,
      minlength: 10,
    },
  },
  errorElement: "span",
  errorPlacement: function (error, element) {
    element.closest("li").append(error);
  },
  submitHandler: function (form) {
  jQuery(".field_error").html("");
  $('.user_error').text('');
  $('.error').text('');
  jQuery("#check_user_btn").html("Please wait...");
  jQuery("#check_user_btn").attr("disabled", true);
    jQuery.ajax({
      url: SITE_URL + "check_user",
      type: "post",
      data: jQuery("#check_user").serialize(),
      success: function (result) {
        var getData = $.parseJSON(result);
        if(getData.status == 1) {
          jQuery("#mobile").attr("disabled", "true");
          jQuery(".otp_box").show();
          jQuery("#bottom-wizard").hide();
          jQuery("#captcha_box").hide();
        }else if(getData.status == 0){
          $('#mobile_error').text(getData.error);
          jQuery("#check_user_btn").attr("disabled", false);
          jQuery("#check_user_btn").html("Submit");
          setTimeout(function(){ window.location.href = '/'; }, '2000');
        }else if(getData.status == -3){
          $('.user_error').text(getData.error);
          jQuery("#check_user_btn").attr("disabled", false);
          jQuery("#check_user_btn").html("Submit");
        }else if (getData.status == -2) {
          jQuery("#check_user_btn").html("Submit");
          jQuery("#check_user_btn").attr("disabled", false);
          grecaptcha.reset();
          var res = getData.validation_array;
          for (const error in res) {
            $(`.${error}`).html(res[error]);
            $("html, body").animate(
              {
                scrollTop: $(`#${error}`).offset().top + "px",
              },
              "fast"
            );
          }
        }
      },
    });
  },
});
function userotpFRM(evt) {
  if(evt.keyCode == 13)
    // $("#signInOtp").submit();
    user_verify_otp();
}  
function user_verify_otp() {
  jQuery(".field_error").html("");
  jQuery("#otp_btn").html("Please wait...");
  jQuery("#otp_btn").attr("disabled", true);
  var otp = jQuery("#otp").val();
  jQuery.ajax({
    url: SITE_URL + "userverifyOTP",
    type: "post",
    data: "otp=" + otp,
    success: function (result) {
      var isError = result.indexOf("elementError");
      if (isError > 0) {
        var getData = $.parseJSON(result);
        $.each(getData, function (val, key) {
          $.each(key, function (fieldVal, fieldKey) {
            $.each(fieldKey, function (fieldVal1, fieldKey1) {
              if (fieldKey1 == "CORRECT_OTP") {
                window.location.href = SITE_URL + "ipv";
              }
              if (fieldKey1 == "OTP_ERROR") {
                jQuery("#otp_error").html("Please enter correct OTP");
              }
            });
          });
        });
      }
      jQuery("#otp_btn").html("Verify OTP ");
      jQuery("#otp_btn").attr("disabled", false);
    },
  });
}

$("#accountclosureLogin").validate({
  rules: {
    mobile: {
      required: true,
      number: true,
      maxlength: 10,
      minlength: 10,
    },
  },
  errorElement: "span",
  errorPlacement: function (error, element) {
    element.closest("li").append(error);
  },
  submitHandler: function (form) {
  jQuery(".field_error").html("");
  $('.user_error').text('');
  $('.error').text('');
  jQuery("#check_user_btn").html("Please wait...");
  jQuery("#check_user_btn").attr("disabled", true);
    jQuery.ajax({
      url: SITE_URL + "check_accountclosure_user",
      type: "post",
      data: jQuery("#accountclosureLogin").serialize(),
      success: function (result) {
        var getData = $.parseJSON(result);
        if(getData.status == 1) {
          jQuery("#mobile").attr("disabled", "true");
          jQuery(".otp_box").show();
          jQuery("#bottom-wizard").hide();
          jQuery("#captcha_box").hide();
        }else if(getData.status == 0){
          $('#mobile_error').text(getData.error);
          jQuery("#check_user_btn").attr("disabled", false);
          jQuery("#check_user_btn").html("Submit");
          setTimeout(function(){ window.location.href = '/'; }, '2000');
        }
      },
    });
  },
});
function accountclosureuserotpFRM(evt) {
  if(evt.keyCode == 13)
    // $("#signInOtp").submit();
    account_closureUser_verify_otp();
}  
function account_closureUser_verify_otp() {
  jQuery(".field_error").html("");
  jQuery("#otp_btn").html("Please wait...");
  jQuery("#otp_btn").attr("disabled", true);
  var otp = jQuery("#otp").val();
  jQuery.ajax({
    url: SITE_URL + "accountClosureUserVerifyOTP",
    type: "post",
    data: "otp=" + otp,
    success: function (result) {
      var isError = result.indexOf("elementError");
      if (isError > 0) {
        var getData = $.parseJSON(result);
        $.each(getData, function (val, key) {
          $.each(key, function (fieldVal, fieldKey) {
            $.each(fieldKey, function (fieldVal1, fieldKey1) {
              if (fieldKey1 == "CORRECT_OTP") {
                window.location.href = SITE_URL + "account_closure";
              }
              if (fieldKey1 == "OTP_ERROR") {
                jQuery("#otp_error").html("Please enter correct OTP");
              }
            });
          });
        });
      }
      jQuery("#otp_btn").html("Verify OTP ");
      jQuery("#otp_btn").attr("disabled", false);
    },
  });
}
function accountclosureuserotpFRM_NEW(evt) {
  if(evt.keyCode == 13)
    // $("#signInOtp").submit();
    account_closureUser_verify_otp_NEW();
}  
function account_closureUser_verify_otp_NEW() {
  jQuery(".field_error").html("");
  jQuery("#otp_btn").html("Please wait...");
  jQuery("#otp_btn").attr("disabled", true);
  var otp = jQuery("#otp").val();
  jQuery.ajax({
    url: SITE_URL + "accountClosureUserVerifyOTP_NEW",
    type: "post",
    data: "otp=" + otp,
    success: function (result) {
      var isError = result.indexOf("elementError");
      if (isError > 0) {
        var getData = $.parseJSON(result);
        $.each(getData, function (val, key) {
          $.each(key, function (fieldVal, fieldKey) {
            $.each(fieldKey, function (fieldVal1, fieldKey1) {
              if (fieldKey1 == "CORRECT_OTP") {
                Swal.fire({
                  title: 'Success',
                  text: 'Successfully submit',
                  icon: 'success',
                  confirmButtonText: 'ok'
                })
                setTimeout(function(){
                  window.location.href = SITE_URL;
                  },2000)
                
              }
              if (fieldKey1 == "OTP_ERROR") {
                jQuery("#otp_error").html("Please enter correct OTP");
              }
            });
          });
        });
      }
      jQuery("#otp_btn").html("Verify OTP ");
      jQuery("#otp_btn").attr("disabled", false);
    },
  });
}
// jQuery.validator.addMethod("lettersonly", function(value, element) {
// 	return this.optional(element) || /^[a-z\s]+$/i.test(value);
// 	}, "Only alphabetical characters");

// jQuery.validator.addMethod("alphanumeric", function(value, element) {
// 		return this.optional(element) || /^[\w.]+$/i.test(value);
// 	}, "Letters And Numbers only please");

$("#frmPersonalInfo").validate({
  rules: {
    father_name: {
      required: true,
      lettersonly: true,
      maxlength: 48,
    },
    mother_name: {
      required: true,
      lettersonly: true,
      maxlength: 48,
    },
    pan_no: {
      required: true,
      alphanumeric: true,
      maxlength: 10,
      minlength: 10,
    },
    aadhaar_number: {
      required: true,
      number: true,
      maxlength: 12,
      minlength: 12,
    },
  },
  errorElement: "span",
  errorPlacement: function (error, element) {
    if (
      element.attr("name") == "gender" ||
      element.attr("name") == "marital_status" ||
      element.attr("name") == "residential_status"
    ) {
      element.closest("ul").find(".error").append(error);
    } else {
      element.closest("li").find(".error").append(error);
    }
  },
  submitHandler: function (form) {
    jQuery(".field_error").html("");
    jQuery(".validation_message_error").html("");
    jQuery("#btnPersonalInfo").html("Please wait...");
    jQuery("#btnPersonalInfo").attr("disabled", true);
    jQuery.ajax({
      url: SITE_URL + "submitPersonalInfo",
      type: "post",
      data: jQuery("#frmPersonalInfo").serialize(),
      success: function (result) {
        var getData = $.parseJSON(result);
			if(getData.status == 1){
        jQuery("#btnPersonalInfo").html("Next");
        jQuery("#btnPersonalInfo").attr("disabled", false);
        jQuery(".step_box").hide();
        jQuery(".step2").show();
			}else if(getData.status == 0){
			  jQuery("#btnPersonalInfo").html("Next");
        jQuery("#btnPersonalInfo").attr("disabled", false);
			  $("#pan_noError").html(getData.msg);
			}else{
			  jQuery("#btnPersonalInfo").html("Next");
        jQuery("#btnPersonalInfo").attr("disabled", false);
			  $("#pan_noError").html("Invalid Pan pattern");
			}
        /*var isError = result.indexOf("elementError");
				if(isError>0){
					
				}*/
        // $(".error").html("");
        // jQuery(".field_error").html("");
       
        jQuery("html, body").animate(
          { scrollTop: $(".step2").offset().top },
          500
        );
      },
    });
  },
});
$("#frmReasonForClosure").validate({
  rules: {
    client_id: {
      required: true,
    },
    dp_id: {
      required: true,
    },
    mobile_number: {
      required: true,
      number: true,
      maxlength: 10,
      minlength: 10,
    },
  
  },
  errorElement: "span",
  errorPlacement: function (error, element) {
      element.closest("li").find(".error").append(error);
  },
  submitHandler: function (form) {
    jQuery(".field_error").html("");
    jQuery("#client_master_file_error").html("");
    jQuery("#mobile_number_error").html("");
    jQuery("#success_msg").html("");
    jQuery("#btnReasonForClosure").html("Please wait...");
    jQuery("#btnReasonForClosure").attr("disabled", true);
    jQuery.ajax({
      type: 'POST',
      url: SITE_URL + "submitReasonForClosure",
      enctype: 'multipart/form-data',
      data: new FormData(form),
      processData: false,
      contentType: false,
      success: function (result) {
        var isError = result.indexOf("elementError");
        if (isError > 0) {
          var getData = $.parseJSON(result);
          $.each(getData, function (val, key) {
            $.each(key, function (fieldVal, fieldKey) {
              $.each(fieldKey, function (fieldVal1, fieldKey1) {
                $("#" + fieldVal1).html(fieldKey1);
                jQuery("#btnReasonForClosure").html("Submit");
                jQuery("#btnReasonForClosure").attr("disabled", false);
              });
            });
          });
        }else{
          var getData = $.parseJSON(result);
          if(getData.status == 1){
            jQuery("#survey_container_otp").show();
            jQuery("#survey_container").hide();
            jQuery("#btnReasonForClosure").html("Submit");
            jQuery("#success_msg").html("Otp sent to your mobile number");
            jQuery("#btnReasonForClosure").attr("disabled", false);

          }
          // alert('Successfully submit');
          // $('#frmReasonForClosure').trigger("reset");
       
          return;
          window.location.href = '/';

        }
       
      },
    });
  },
});
// jQuery('#frmPersonalInfo').on('submit',function(e){
// 	jQuery('.field_error').html('');
// 	jQuery('#btnPersonalInfo').html('Please wait...');
// 	jQuery('#btnPersonalInfo').attr('disabled',true);
// 	jQuery.ajax({
// 		url:SITE_URL+'submitPersonalInfo',
// 		type:'post',
// 		data:jQuery('#frmPersonalInfo').serialize(),
// 		success:function(result){
// 			/*var isError = result.indexOf("elementError");
// 			if(isError>0){

// 			}*/
// 			jQuery('.field_error').html('');
// 			jQuery('#btnPersonalInfo').html('Next');
// 	        jQuery('#btnPersonalInfo').attr('disabled',false);
// 			jQuery('.step_box').hide();
// 			jQuery('.step2').show();
// 			jQuery('html, body').animate({ scrollTop: $('.step2').offset().top }, 500);
// 		}
// 	});
// 	e.preventDefault();
// });

$("#frmAddress").validate({
  rules: {
    correspondence_address_pincode: {
      maxlength: 9,
    },
    permanent_address_pincode: {
      maxlength: 9,
    },
  },
  errorElement: "span",
  errorPlacement: function (error, element) {
    if (
      element.attr("name") == "permanent_address_country" ||
      element.attr("name") == "correspondence_address_country"
    ) {
      element.closest("ul").find(".error").append(error);
    } else {
      element.closest("li").append(error);
    }
  },
  submitHandler: function (form) {
    jQuery(".field_error").html("");
    jQuery("#btnAddress").html("Please wait...");
    jQuery("#btnAddress").attr("disabled", true);
    jQuery.ajax({
      url: SITE_URL + "submitAddress",
      type: "post",
      data: jQuery("#frmAddress").serialize(),
      success: function (result) {
        /*var isError = result.indexOf("elementError");
				if(isError>0){
					
				}*/
        $(".error").html("");
        jQuery(".field_error").html("");
        jQuery("#btnAddress").html("Next");
        jQuery("#btnAddress").attr("disabled", false);
        jQuery(".step_box").hide();
        jQuery(".step3").show();
        jQuery("html, body").animate(
          { scrollTop: $(".step3").offset().top },
          500
        );
      },
    });
  },
});
// jQuery('#frmAddress').on('submit',function(e){
// 	jQuery('.field_error').html('');
// 	jQuery('#btnAddress').html('Please wait...');
// 	jQuery('#btnAddress').attr('disabled',true);
// 	jQuery.ajax({
// 		url:SITE_URL+'submitAddress',
// 		type:'post',
// 		data:jQuery('#frmAddress').serialize(),
// 		success:function(result){
// 			jQuery('.field_error').html('');
// 			jQuery('#btnAddress').html('Next');
// 	        jQuery('#btnAddress').attr('disabled',false);
// 			jQuery('.step_box').hide();
// 			jQuery('.step3').show();
// 			jQuery('html, body').animate({ scrollTop: $('.step3').offset().top }, 500);
// 		}
// 	});
// 	e.preventDefault();
// });
$("#frmBankDetails").validate({
  rules: {
    account_number: {
      maxlength: 20,
    },
  },
  errorElement: "span",
  errorPlacement: function (error, element) {
    element.closest("li").append(error);
  },
  submitHandler: function (form) {
    jQuery(".field_error").html("");
    jQuery("#bank_Error").html("");

    jQuery("#btnBankDetails").html("Please wait...");
    jQuery("#btnBankDetails").attr("disabled", true);
    jQuery.ajax({
      url: SITE_URL + "submitBankDetails",
      type: "post",
      data: jQuery("#frmBankDetails").serialize(),
      success: function (result) {
        /*var isError = result.indexOf("elementError");
				if(isError>0){
					
				}*/
        var getData = $.parseJSON(result);
        //console.log(getData);
        if(getData.status == 1){
         // $("#bank_success").html(getData.msg);
          $(".error").html("");
          jQuery(".field_error").html("");
          jQuery("#account_number").attr("readonly", true);
          jQuery("#btnBankDetails").html("Next");
          jQuery("#btnBankDetails").attr("disabled", false);
          jQuery(".step_box").hide();
          jQuery(".step4").show();
          }else if(getData.status == 0){
          $("#bank_Error").html(getData.msg);
          }else{
          $("#bank_Error").html("Invalid account number or ifsc provided");
         
          }
          setTimeout(showbtn, 60000);
          function showbtn(){
            jQuery("#btnBankDetails").html("Next");
            jQuery("#btnBankDetails").attr("disabled", false);
          }
        jQuery("html, body").animate(
          { scrollTop: $(".step3").offset().top },
          500
        );
      },
    });
  },
});
// jQuery('#frmBankDetails').on('submit',function(e){
// 	jQuery('.field_error').html('');
// 	jQuery('#btnBankDetails').html('Please wait...');
// 	jQuery('#btnBankDetails').attr('disabled',true);
// 	jQuery.ajax({
// 		url:SITE_URL+'submitBankDetails',
// 		type:'post',
// 		data:jQuery('#frmBankDetails').serialize(),
// 		success:function(result){
// 			jQuery('.field_error').html('');
// 			jQuery('#btnBankDetails').html('Next');
// 	        jQuery('#btnBankDetails').attr('disabled',false);
// 			jQuery('.step_box').hide();
// 			jQuery('.step4').show();
// 			jQuery('html, body').animate({ scrollTop: $('.step3').offset().top }, 500);
// 		}
// 	});
// 	e.preventDefault();
// });

jQuery("#frmMarketSegments").on("submit", function (e) {
  jQuery(".field_error").html("");
  jQuery("#btnMarketSegments").html("Please wait...");
  jQuery("#btnMarketSegments").attr("disabled", true);
  jQuery.ajax({
    url: SITE_URL + "submitMarketSegments",
    type: "post",
    data: jQuery("#frmMarketSegments").serialize(),
    success: function (result) {
      jQuery(".field_error").html("");
      jQuery("#btnMarketSegments").html("Next");
      jQuery("#btnMarketSegments").attr("disabled", false);
      jQuery(".step_box").hide();
      jQuery(".step5").show();
      jQuery("html, body").animate(
        { scrollTop: $(".step3").offset().top },
        500
      );
    },
  });
  e.preventDefault();
});

jQuery("#frmRegulatoryInfo").on("submit", function (e) {
  jQuery(".field_error").html("");
  jQuery("#btnRegulatoryInfo").html("Please wait...");
  jQuery("#btnRegulatoryInfo").attr("disabled", true);
  jQuery.ajax({
    url: SITE_URL + "submitRegulatoryInfo",
    type: "post",
    data: jQuery("#frmRegulatoryInfo").serialize(),
    success: function (result) {
      jQuery(".field_error").html("");
      jQuery("#btnRegulatoryInfo").html("Next");
      jQuery("#btnRegulatoryInfo").attr("disabled", false);
      jQuery(".step_box").hide();
      jQuery(".step8").show();
      jQuery("html, body").animate(
        { scrollTop: $(".step3").offset().top },
        500
      );
    },
  });
  e.preventDefault();
});
// start nomination form

var nomnee_count = $(".nominee_Details").length;
if (nomnee_count >= 3) $("#add_nominee").hide();

$("#add_nominee").click(function () {
  nomnee_count++;
  if (nomnee_count <= 3)
    $("#nominee_field").append(
      ` 
      <div class="nominee_Details" id="${"row" + nomnee_count}"> 
        <div class="row">
            <div class="col-md-12">
              <h4 style="display: table;width: 100%;">Nominee(s) ${nomnee_count} Details</h4>
            </div>
        </div>
        <div class="row">
          <div class="col-md-4">
            <div class="form-group">
              <label  for="name_of_nominee_${nomnee_count - 1}" >Name of Nominee(s) (Mr./Ms.) </label>
              <input type="text" class="form-control" id="name_of_nominee_${nomnee_count - 1}" placeholder="" name="name_of_nominee[]" required>
            </div>
          </div>
          <div class="col-md-4">
            <div class="form-group">
              <label for="nominee_mobile_${nomnee_count - 1}" >Mobile</label>
              <input type="number" class="form-control" placeholder=""  id="nominee_mobile_${nomnee_count - 1}" name="nominee_mobile[]" minlength="10" maxlength="10" required>
            </div>
          </div>
          <div class="col-md-4">
            <div class="form-group">
              <label  for="nominee_email_${nomnee_count - 1}" >Email ID</label>
              <input type="email" class="form-control" placeholder="" id="nominee_email_${nomnee_count - 1}" name="nominee_email[]"  required>
              <div class="nominee_first_email error"> </div>
            </div>
          </div>
          <div class="col-md-4">
            <div class="form-group input-group">
              <label style="margin-bottom: 25px;" >Share of Nominee(s)  </label>
              <input type="number" class="form-control nominee_share" placeholder=""    name="share_of_nominees[]" >
              <div class="input-group-append">
                <span class="input-group-text">%</span>
              </div>
             
            </div>
          </div>
          <div class="col-md-4">
            <div class="form-group">
              <label>Relation With the Applicant (If Any) Nominee(s) </label>
              <input type="text" class="form-control" placeholder="" name="relation_applicant_name_nominees[]"  >
              
            </div>
          </div>
        </div>
        <div class="row">
          <div class="col-md-4">
            <div class="form-group">
              <label for="nominee_address_${nomnee_count - 1}">Address</label>
              <input type="text" class="form-control" placeholder="" id="nominee_address_${nomnee_count - 1}" name="nominee_address[]"  required>               
            </div>
          </div>
          <div class="col-md-4">
            <div class="form-group">
              <label for="nominee_city_${nomnee_count - 1}">City/Place</label>
              <input type="text" class="form-control" placeholder="" id="nominee_city_${nomnee_count - 1}" name="nominee_city[]"  required>
              <div class="nominee_first_city error"> </div>
            </div>
          </div>
          <div class="col-md-4">
            <div class="form-group">
              <label for="nominee_state_${nomnee_count - 1}">State</label>
              <input type="text" class="form-control" placeholder="" id="nominee_state_${nomnee_count - 1}" name="nominee_state[]"  required>
              <div class="nominee_first_state error"> </div>
            </div>
          </div>
          <div class="col-md-4">
            <div class="form-group">
              <label for="country_${nomnee_count - 1}">Country</label>
                  ${country(nomnee_count -1)}
              <div class="nominees_first_country error"> </div>
            </div>
          </div>
          <div class="col-md-4">
            <div class="form-group">
              <label for="nominee_pin_code_${nomnee_count - 1}">Pin Code</label>
              <input type="number" class="form-control" placeholder="" id="nominee_pin_code_${nomnee_count - 1}"  name="nominee_pin_code[]"  required>
              <div class="nominee_first_pin_code error"> </div>
            </div>
          </div>			  
        </div>
		<div class="row">
			  <div class="col-md-12">
				  <h6 style="display: table;width: 100%;">Nominee Identification Details</h6>
				  <span>[Please tick any one of following and provide details of same]</span>
				  <div class="form-group tick_box" style="margin-top: 15px;">
					  <input type="radio" id="Photograph${nomnee_count}" class="check_radio option-input" value="photograph" name="nominee_identification_${nomnee_count-1}"  required  >
					  <label for="Photograph" class="label_gender"> Photograph & Signature</label><br>
					  <input type="radio" id="pan${nomnee_count}" class=" check_radio option-input" value="pan" name="nominee_identification_${nomnee_count-1}" >
					  <label for="pan" class="label_gender"> PAN</label><br>
					  <input type="radio" id="Aadhaar${nomnee_count}" class=" check_radio option-input" value="aadhaar" name="nominee_identification_${nomnee_count-1}" >
					  <label for="Aadhaar" class="label_gender"> Aadhaar</label><br>
					  <input type="radio" id="bank${nomnee_count}" class=" check_radio option-input" value="saving_bank_account_no" name="nominee_identification_${nomnee_count-1}" >
					  <label for="bank" class="label_gender"> Saving Bank Account No.</label><br>
					  <input type="radio" id="identity${nomnee_count}" class=" check_radio option-input" value="proof_of_identity" name="nominee_identification_${nomnee_count-1}" >
					  <label for="identity" class="label_gender"> Proof of Identity</label><br>
					  <input type="radio" id="demat${nomnee_count}" class=" check_radio option-input" value="demat_account_iD" name="nominee_identification_${nomnee_count-1}" >
					  <label for="demat" class="label_gender">Demat Account ID</label>
            <div class="nominee_first_identification error"> </div>
				  </div>	
          <input type="hidden" name="radio_index[]" value="${nomnee_count-1}">
			  </div>
        <div class="col-md-12">
          <h6 style="display: table;width: 100%;">Please Upload Selected Nominee Identification Document</h6>
          <div class="form-group">
              <label>Document</label>
              <input type="file" class="form-control" placeholder="" name="nominee_document_${nomnee_count-1}" required >
              <div class="nominee_document error"> </div>
          </div>	
        </div>
	 </div>
          
					<div class="row">
                  <div class="col-md-12">
                  <button type="button" name="remove" id="${nomnee_count}" class="btn btn-danger btn_remove">Remove</button>  
					</div>
					</br>
               </div>
               `
    );
  let share = 100 / nomnee_count;
  $(".nominee_share").val(share.toFixed(2));
  if (nomnee_count >= 3) $(this).hide();
});
function delete_nomination_details(id,key){
  if (confirm('Are you sure?')) {
    jQuery.ajax({
      url: SITE_URL + "delete_nomination_details?id="+id,
      type: "get",
      contentType: false,
      processData: false,
      success: function (result) {
        const obj = JSON.parse(result);
        if(obj.status == 1){
          if (nomnee_count <= 3) $("#add_nominee").show();
          var button_id = $(key).attr("id");
          $("#row" + button_id + "").remove();
          nomnee_count--;
          if (button_id == nomnee_count){ 
            button_id++;
              $(`#row${button_id} input,#row${button_id} select`).each(function(i){
                // var name = $(this).attr('name'); 
                // name = name.replace(/\[\d+\]/g, '[' + nomnee_count + ']');
                // $(this).attr('name',  name);
                $(`#row${button_id} h4`).text(`Nominee(s) ${nomnee_count} Details`);
                $(`#row${button_id}`).attr(`id`,`row${nomnee_count}`);
                $(`#${button_id}`).attr(`id`,`${nomnee_count}`);
              });
            }
            let share = 100 / nomnee_count;
            $(".nominee_share").val(share.toFixed(2));
        }
           
      },
    });
  }   
}


$(document).on("click", ".btn_remove", function () {
  if (nomnee_count <= 3) $("#add_nominee").show();
  var button_id = $(this).attr("id");
  $("#row" + button_id + "").remove();
  nomnee_count--;
  if (button_id == nomnee_count){ 
	button_id++;
    $(`#row${button_id} input,#row${button_id} select`).each(function(i){
      // var name = $(this).attr('name'); 
      // name = name.replace(/\[\d+\]/g, '[' + nomnee_count + ']');
      // $(this).attr('name',  name);
      $(`#row${button_id} h4`).text(`Nominee(s) ${nomnee_count} Details`);
      $(`#row${button_id}`).attr(`id`,`row${nomnee_count}`);
      $(`#${button_id}`).attr(`id`,`${nomnee_count}`);
    });
  }
  let share = 100 / nomnee_count;
  $(".nominee_share").val(share.toFixed(2));
});

$(`input[name="nominee_minor"]`).on("change", function () {
  let val = $('input[name=nominee_minor]:checked').val();
  if(val == "1"){
    $(".guardian_section").show();
    $(".guardian_section input, select").prop("required", true);
    $(".guardian_section .tick_box input").prop("required", false);
    $(".guardian_section .tick_box input:first").prop("required", true);
    $(".guardian_document_exist").prop("required", false);
  }
  else{
    $(".guardian_section").hide();
    $(".guardian_section input, select").prop("required", false);    
  }
});

function nominationFormErrorPlacement(error, element) {
  // console.log(element);
  element.closest("div").append(error);
 // $(element[0]).closest("div").append(error);
  // if()
  // if (
  //   element.attr("name") == "nominee_identification[]" 
  // ) {
  //   element.closest("div").append(error);
  // } else {
  // }
}

// $.validator.prototype.errorsFor = function (element) {
//   var name = this.idOrName(element);
//   console.log(name);
//   var elementParent = element.parentElement;
//   return this.errors().filter(function() {
//       return $(this).attr('for') == name && $(this).parent().is(elementParent);
//   });
// }

const nominationFormRules = {
  "name_of_nominee[]": {
    lettersonly: true,
    maxlength: 45,
  },
  "nominee_mobile[]": {
    maxlength: 10,
    minlength: 10,
  },
};

jQuery("#nomination_form").validate({
  rules: nominationFormRules,
  errorElement: "span",
  errorPlacement: nominationFormErrorPlacement,
  submitHandler: function () {
    var formData = $("#nomination_form").submit(function (e) {
      return;
    });
    var formData = new FormData(formData[0]);
    jQuery(".field_error").html("");
    // jQuery("#nominationform").html("Please wait...");
    // jQuery("#nominationform").attr("disabled", true);
    jQuery.ajax({
      url: SITE_URL + "submitnominationform",
      type: "post",
      dataType: "json",
      contentType: "application/octet-stream",
      enctype: "multipart/form-data",
      contentType: false,
      processData: false,
      data: formData,
      success: function (result) {
        jQuery(".field_error").html("");
        jQuery(".error").html("");
        jQuery("#nominationform").html("Next");
        jQuery("#nominationform").attr("disabled", false);
        jQuery(".step_box").hide();
        // jQuery(".step9").show();
        jQuery(".step6").show();
        jQuery("html, body").animate(
          { scrollTop: $(".step3").offset().top },
          500
        );
      },
    });
  },
});

jQuery("#nomination_form").on("submit", function (e) {
jQuery("#nomination_form").valid()

});

// jQuery('#nomination_form').on('submit',function(e){

// 	jQuery('.error').html('');
// 	jQuery('.field_error').html('');
// 	// jQuery('#nominationform').html('Please wait...');
// 	// jQuery('#nominationform').attr('disabled',true);
// 	jQuery.ajax({
// 		url: SITE_URL+'submitnominationform',
// 		type:'post',
// 		data:  new FormData(this),
// 		contentType: false,
// 			  cache: false,
// 		processData:false,
// 		success:function(result){
// 			var obj = JSON.parse(result);
// 			if(obj.status == 1){
//             jQuery('.field_error').html('');
// 			jQuery('.error').html('');
// 			jQuery('#nominationform').html('Next');
// 	        jQuery('#nominationform').attr('disabled',false);
// 			jQuery('.step_box').hide();
// 			jQuery('.step9').show();
// 			jQuery('html, body').animate({ scrollTop: $('.step3').offset().top }, 500);
// 			}else if(obj.status == -2){
// 				jQuery('#nominationform').html('Next');
// 				jQuery('#nominationform').attr('disabled',false);
// 				var res = obj.validation_array;
// 				for (const error in res) {
// 					 let err =error.replace("[]", "");
// 					console.log(err);
// 					 $(`.${err}`).html(res[error]);
// 					$("html, body").animate(
// 					  {
// 						scrollTop: $(`.${err}`).offset().top + "px",
// 					  },
// 					  "fast"
// 					);

// 				}
// 			}
// 		}
// 	});
// 	e.preventDefault();
// });

jQuery("#frmNomineeVerificationMobile").on("submit", function (e) {
  jQuery(".error").html("");
  jQuery(".field_error").html("");
  // jQuery('.Verification_success_msg').html("");
  jQuery("#btnNomineeVerification").html("Please wait...");
  jQuery.ajax({
    url: SITE_URL + "submitNominationVerificationMobile",
    type: "post",
    data: new FormData(this),
    contentType: false,
    cache: false,
    processData: false,
    success: function (result) {
      var obj = JSON.parse(result);
      if (obj.status == 1) {
        jQuery(".field_error").html("");
        jQuery(".error").html("");
        jQuery("#btnNomineeVerification").html("Next");
        //jQuery('.Verification_success_msg').html(obj.message);
        jQuery(".step_box").hide();
        jQuery(".step6").show();
        jQuery("html, body").animate(
          { scrollTop: $(".step3").offset().top },
          500
        );
      } else if (obj.status == -2) {
        jQuery("#btnNomineeVerification").html("Next");
        jQuery("#nominationform").attr("disabled", false);
        grecaptcha.reset();
        var res = obj.validation_array;
        for (const error in res) {
          $(`.${error}`).html(res[error]);
          $("html, body").animate(
            {
              scrollTop: $(`#${error}`).offset().top + "px",
            },
            "fast"
          );
        }
      }
    },
  });
  e.preventDefault();
});
jQuery("#frmDisclosures").on("submit", function (e) {
  jQuery(".field_error").html("");
  jQuery("#btnDisclosures").html("Please wait...");
  jQuery("#btnDisclosures").attr("disabled", true);
  jQuery.ajax({
    url: SITE_URL + "submitDisclosures",
    type: "post",
    data: jQuery("#frmDisclosures").serialize(),
    success: function (result) {
      /*jQuery('.field_error').html('');
			jQuery('#btnDisclosures').html('Next');
	        jQuery('#btnDisclosures').attr('disabled',false);	
			jQuery('.step_box').hide();
			jQuery('.step7').show();
			jQuery('html, body').animate({ scrollTop: $('.step3').offset().top }, 500);*/
      window.location.href = SITE_URL + "thank_you";
    },
  });
  e.preventDefault();
});

jQuery("#frmKYCDocuments").on("submit", function (e) {
  jQuery(".field_error").html("");
  jQuery("#btnKYCDocuments").html("Please wait...");
  jQuery("#btnKYCDocuments").attr("disabled", true);
  jQuery.ajax({
    url: SITE_URL + "submitKYCDocuments",
    type: "post",
    data: new FormData(this),
    processData: false,
    contentType: false,
    success: function (result) {
      var isError = result.indexOf("elementError");
      if (isError > 0) {
        var getData = $.parseJSON(result);
        $.each(getData, function (val, key) {
          $.each(key, function (fieldVal, fieldKey) {
            $.each(fieldKey, function (fieldVal1, fieldKey1) {
              $("#" + fieldVal1).html(fieldKey1);
            });
          });
        });
      } else {
        jQuery("#btnKYCDocuments").html("Document uploaded successfully...");
        setTimeout(function () {
          window.location.href = SITE_URL + "thank_you";
        }, 3000);
      }
    },
  });
  e.preventDefault();
});

function step_change(id) {
  jQuery(".step_box").hide();
  jQuery(".step" + id).show();
  $("html, body").animate({ scrollTop: $(".step" + id).offset().top }, 500);
}
jQuery(document).ready(function () {
  jQuery("#same_as_permanent").change(function () {
    if (jQuery(this).is(":checked")) {
      jQuery("#correspondence_address_box").show();
      jQuery(
        "#correspondence_address1,#correspondence_address_city,#correspondence_address_country,#correspondence_address_pincode"
      ).attr("required", "");
    } else {
      jQuery("#correspondence_address_box").hide();
      jQuery(
        "#correspondence_address1,#correspondence_address_city,#correspondence_address_country,#correspondence_address_pincode"
      ).removeAttr("required");
    }
  });
  $("#is_politically_exposed").change(function () {
    if ($("option:selected", this).val() == "Yes") {
      jQuery(".politically_exposed_box").show();
    } else {
      jQuery(".politically_exposed_box").hide();
    }
  });
  $("#is_action_sebi").change(function () {
    if ($("option:selected", this).val() == "Yes") {
      jQuery("#is_action_sebi_box").show();
    } else {
      jQuery("#is_action_sebi_box").hide();
    }
  });
  jQuery("#is_any_disputes").change(function () {
    if (jQuery(this).is(":checked")) {
      jQuery("#details_of_disputes_box").show();
    } else {
      jQuery("#details_of_disputes_box").hide();
    }
  });
});

$(window).load(function () {
  //$(" input").val("");

  $(".input-effect input").focusout(function () {
    if ($(this).val() != "") {
      $(this).addClass("has-content");
    } else {
      $(this).removeClass("has-content");
    }
  });
  $("select").focusout(function () {
    if ($(this).val() != "") {
      $(this).addClass("has-content");
    } else {
      $(this).removeClass("has-content");
    }
  });

  $(
    "#otp,#dob,#mob,#yob,#correspondence_address_pincode,#permanent_address_pincode"
  ).keypress(function (e) {
    if (e.which != 8 && e.which != 0 && (e.which < 48 || e.which > 57)) {
      return false;
    }
  });
});

function showDocUpload() {
  jQuery("#kyc_documents").show();
}

$("#verfyIfsc").click(function () {
  $(".validation_message_error").html("");
  var ifsc_code = $("#ifsc_code_i").val();
  var isError = "";
  if (ifsc_code == "") {
    $("#ifsc_codeError").html("Please Enter IFSC Code");
    isError = "yes";
  }
  if (isError == "") {
    jQuery.ajax({
      url: SITE_URL + "verifyIfscCode",
      type: "post",
      data: "ifscCode=" + ifsc_code,
      success: function (result) {
        var getData = $.parseJSON(result);
        $.each(getData, function (key, val) {
          if (key == "error") {
            $("#ifsc_codeError").html(val);
          } else if (key == "class") {
            $("#ifscBox").removeClass("hide");
            $("#ifsc_code_i").attr("readonly", "readonly");
            $("#verfyIfsc").addClass("hide");
            $("#chngeIfsc").removeClass("hide");
            $("#btnBankDetails").removeAttr("disabled");
          } else {
            $("#" + key).val(val);
          }
        });
      },
    });
  }
});

$("#chngeIfsc").click(function () {
  $("#bank,#branch,#address").val("");
  $("#ifscBox").addClass("hide");
  $("#ifsc_code_i").removeAttr("readonly");
  $("#verfyIfsc").removeClass("hide");
  $("#chngeIfsc").addClass("hide");
  $("#btnBankDetails").attr("disabled", "disabled");
});

$("#verfyBank").click(function () {
  $(".validation_message_error").html("");
  $(".validation_message_success").html("");
  $("#verfyBank").prop('disabled', true);
  
	let btn = $("#verfyBank").html();
	$("#verfyBank").html('Proccessing...');
  var ifsc_code = $("#ifsc_code_i").val();
  var account_number = $("#account_number").val();
  var isError = "";
  if (ifsc_code == "") {
    $("#verfyBank").prop('disabled', false);
         $("#verfyBank").html(btn);
    $("#ifsc_codeError").html("Please Enter IFSC Code");
    isError = "yes";
  }
  if (account_number == "") {
    $("#verfyBank").prop('disabled', false);
         $("#verfyBank").html(btn);
    $("#account_numberError").html("Please Enter Account Number");
    isError = "yes";
  }
  
  if (isError == "") {
    $("#verfyBank").hide();
    jQuery.ajax({
      url: SITE_URL + "verfyBank",
      type: "post",
      data: "ifscCode=" + ifsc_code + '&account_number='+account_number,
      success: function (result) {
        var getData = $.parseJSON(result);
        if(getData.status == 1){
          $("#bank_success").html(getData.msg);
            $("#ifsc_code_i").attr("readonly", "readonly");
            $("#account_number").attr("readonly", "readonly");
            $("#verfyBank").addClass("hide");
            // $("#chngeBank").removeClass("hide");
            // $("#chngeBank").removeAttr("style");
            $("#btnBankDetails").removeAttr("disabled");
            $("#verfyBank").prop('disabled', false);
         $("#verfyBank").html(btn);
        }else if(getData.status == 0){
          $("#verfyBank").prop('disabled', false);
         $("#verfyBank").html(btn);
          $("#bank_Error").html(getData.msg);
        }else{
          $("#verfyBank").prop('disabled', false);
         $("#verfyBank").html(btn);
          $("#bank_Error").html("Invalid account number or ifsc provided");
        }
        setTimeout(showbtn, 60000);
        function showbtn(){
          $("#verfyBank").show();
        }
      },
    });
  }
});
$("#chngeBank").click(function () {
  $(".validation_message_error").html("");
  $(".validation_message_success").html("");
  $("#ifsc_code_i").removeAttr("readonly");
  $("#account_number").removeAttr("readonly");
  $("#verfyBank").removeClass("hide");
  $("#chngeBank").addClass("hide");
  $("#verfyBank").removeAttr("style");
  $("#btnBankDetails").attr("disabled", "disabled");
});
let pan;
let aadhaar;
$("#pan_no").on('change', function(){
  pan = false;
  $(".validation_message_error").html("");
  $("#pan_noError").html("");
  var pan_code = $("#pan_no").val();
  var isError = "";
  if (pan_code == "") {
    $("#pan_noError").html("Please Enter PAN");
    isError = "yes";
  }
  if (isError == "") {
    jQuery.ajax({
      url: SITE_URL + "verifyPanCode",
      type: "post",
      data: "pan_code=" + pan_code,
      success: function (result) {
        var getData = $.parseJSON(result);
        if(getData.status == 1){
          $("#pan_noSuccess").html(getData.msg);
          pan = true;
          if(pan == true){
            $("#btnPersonalInfo").removeAttr("disabled");
          }
        }else if(getData.status == 0){
          $("#pan_noError").html(getData.msg);
        }else{
          $("#pan_noError").html("Invalid Pan pattern");
        }
         
      },
    });
  }
  
})

$("#aadhaar_number").on('change', function(){
  aadhaar = false;
  $(".validation_message_error").html("");
  $("#aadhaar_numberError").html("");
  var aadhaar_number = $("#aadhaar_number").val();
  var isError = "";
  if (aadhaar_number == "") {
    $("#aadhaar_numberError").html("Please Enter Aadhaar Number");
    isError = "yes";
  }
  if (isError == "") {
    jQuery.ajax({
      url: SITE_URL + "verifyAadhaarNumber",
      type: "post",
      data: "aadhaar_number=" + aadhaar_number,
      success: function (result) {
        var getData = $.parseJSON(result);
        if(getData.status == 1){
          $("#aadhaar_numberError").html(getData.msg);
          aadhaar = true;
          if(pan == true && aadhaar == true){
            $("#btnPersonalInfo").removeAttr("disabled");
          }
        }else if(getData.status == 0){
          $("#aadhaar_numberError").html(getData.msg);
       
        }else{
          $("#aadhaar_numberError").html("Invalid Aadhaar Number pattern");
        }
         
      },
    });
  }
  
})