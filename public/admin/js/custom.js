	function checkAdmin(){
	var uemail=$('#username').val();
	var upwd=$('#password').val();
	$(".field_error").html('');
	$("#errorMessage").show();
	var is_error='';
	if(uemail==''){
		jQuery('#username_error').html('Please enter username');
		is_error='yes';
	}if(upwd==''){
		jQuery('#password_error').html('Please enter password');
		is_error='yes';
	}
	
	if(is_error==''){
		$('.loginmsg').fadeIn(400).html('Please Wait...');
		var dataString = 'uemail='+ uemail + '&upwd='+ upwd;
		$.ajax({
		   type: "POST",
		   url: SITE_URL+"authenticate",
		   data: dataString,
		   cache: false,
		   success: function(result){
				if(result=='done'){
					window.location=SITE_URL+'dashboard';
				}else{
					var position=result.indexOf("||");
					var warningMessage=result.substring(0,position);
					var errorMessage=result.substring(position+2);
					$('.loginmsg').html(errorMessage);
					return false;
				}
				$("#errorMessage").hide();
		   }
		});
	}
}
$(function(){
	$(`input[name="nominee_minor"]`).trigger("change");
  });
jQuery('#frmaddRole').on('submit',function(e){ 
	 jQuery('.field_error').html('');
	jQuery.ajax({
		url: SITE_URL+'submitaddRollform',
		type:'post',
		data:  new FormData(this),
		contentType: false,
			  cache: false,
		processData:false,
		success:function(result){  
			var obj = JSON.parse(result);
			if(obj.status == 1){ 
				jQuery('.field_error').html('');
				Swal.fire({
					icon: 'success',
					title: 'Success',
					text: obj.message,
				  })
			  setTimeout(window.location.href = "all_roles", 3000);
			}else if(obj.status == 0){
				Swal.fire({
					icon: 'error',
					title: 'Error',
					text: obj.error,
				  })
			}else if(obj.status == -2){ 
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
		}
	});
	e.preventDefault();
});
function deleteRolefunction(id,key){
	Swal.fire({
		title: "Are you sure?",
		text: "You won't be able to revert this!",
		icon: "warning",
		showCancelButton: true,
		confirmButtonColor: "#3085d6",
		cancelButtonColor: "#d33",
		confirmButtonText: "Yes, delete it!",
	  }).then((result) => {
		if (result.isConfirmed) {
			jQuery.ajax({
				url: SITE_URL+'DeleteRoleUser?id='+id,
				type:'get',
				success:function(result){  
					$(key).closest("tr").remove();
					Swal.fire("Deleted!", "Deleted successfully", "success");
				}
			});
		}
	  });
}
function deleteIpvfunction(id,key){
	Swal.fire({
		title: "Are you sure?",
		text: "You won't be able to revert this!",
		icon: "warning",
		showCancelButton: true,
		confirmButtonColor: "#3085d6",
		cancelButtonColor: "#d33",
		confirmButtonText: "Yes, delete it!",
	  }).then((result) => {
		if (result.isConfirmed) {
			jQuery.ajax({
				url: SITE_URL+'DeleteIpvUser?id='+id,
				type:'get',
				success:function(result){  
					$(key).closest("tr").remove();
					Swal.fire("Deleted!", "Deleted successfully", "success");
				}
			});
		}
	  });
}
jQuery('#frmeditRole').on('submit',function(e){ 
	jQuery('.field_error').html('');
   jQuery.ajax({
	   url: SITE_URL+'submiteditRollform',
	   type:'post',
	   data:  new FormData(this),
	   contentType: false,
			 cache: false,
	   processData:false,
	   success:function(result){  
		   var obj = JSON.parse(result);
		   if(obj.status == 1){ 
			   jQuery('.field_error').html('');
			   Swal.fire({
				   icon: 'success',
				   title: 'Success',
				   text: obj.message,
				 })
			 setTimeout(window.location.href = "all_roles", 3000);
		   }else if(obj.status == 0){
			   Swal.fire({
				   icon: 'error',
				   title: 'Error',
				   text: obj.error,
				 })
		   }else if(obj.status == -2){ 
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
	   }
   });
   e.preventDefault();
});
function checkUser(){
	var uemail=$('#email').val();
	var upwd=$('#password').val();
	$("#errorMessage").show();
	
	$('.loginmsg').fadeIn(400).html('Please Wait <img src="'+SITE_IMAGES+'loaders/loader6.gif" />');
	var dataString = 'uemail='+ uemail + '&upwd='+ upwd;
	$.ajax({
	   type: "POST",
	   url: SITE_URL+"user_authenticate",
	   data: dataString,
	   cache: false,
	   success: function(result){
			if(result=='done'){
				window.location=SITE_URL+'dashboard';
			}else{
				var position=result.indexOf("||");
				var warningMessage=result.substring(0,position);
				var errorMessage=result.substring(position+2);
				$('.loginmsg').html(errorMessage);
				return false;
			}
			$("#errorMessage").hide();
	   }
	});
}

function statusChange(t,s,i){
	var operationStation='1';
	if(s=='-1'){
		var result = confirm("Want to delete?");
		if (result) {
		
		}else{
			operationStation='2';
		}
	}
	
	if(operationStation=='1'){
	  var thisobj=$(this);
	  var dataString = 'i=' + i + '&t=' + t + '&s=' + s;
	  $.ajax({
	  type: "POST",
	  url: SITE_URL+"status",
	  data: dataString,
	  cache: false,
	  success: function(html){
			if(s=='-1'){
				var curl=window.location.href;
				window.location.href=curl;
				
			}else{
				if(html=="1")
				{
					$('#box'+i).html("<a title='Active' href='javascript:void(0)' onclick=statusChange('"+t+"','0','"+i+"')>"+ACTIVE_ICON+"</a>");
				}else if(html=="0")
				{
					$('#box'+i).html("<a title='Deactive' href='javascript:void(0)' onclick=statusChange('"+t+"','1','"+i+"')>"+DEACTIVE_ICON+"</a>");
				}
			}
			
		}
	  });
	  }
	  
}


function paymentStatusChange(t,s,i){
	var dataString = 'i=' + i + '&t=' + t + '&s=' + s;
	$.ajax({
		type: "POST",
		url: SITE_URL+"paymentStatusChange",
		data: dataString,
		cache: false,
		success: function(result){
			if(result=="1"){
				$('#box'+i).html("<a title='Accept' href='javascript:void(0)' onclick=paymentStatusChange('"+t+"','0','"+i+"')>Accept</a>");
			}else if(result=="0"){
				$('#box'+i).html("<a title='Pending' href='javascript:void(0)' onclick=paymentStatusChange('"+t+"','1','"+i+"')>Pending</a>");
			} 
		}
	});	  
}

function sortGrid(fieldName){
	$('#orderByField').val(fieldName);
	var orderByFieldValue=$("#orderByFieldValue").val();
	if(orderByFieldValue=='asc'){
		$("#orderByFieldValue").val('desc');
	}else{
		$("#orderByFieldValue").val('asc');
	}
	
	$('#orderByFieldValueChange').val('yes');
	reloadForm();
}

function searchDataBox(s_i){
	$(".sort_by_list").hide();
	if(s_i=='-1'){
		$('#searchByField').val('')
		$('#searchByFieldValue').val('');
		
		reloadForm();
	}else{
		$('#searchByField').val(s_i)
		$("#search_grid_box").show();
		$("#grid_search_button").show();
		if(s_i=='purchase_from_mill.purchase_date'){
			jQuery('#search_grid_box').addClass('purchase_date1');
			$('.purchase_date1').datepicker();
		}else{
			$('.purchase_date1').data('datepicker').remove();
		}
	}
}

function gridSearchData(){
	var search_grid_box=$('#search_grid_box').val();
	$('#searchByFieldValue').val(search_grid_box);
	
	reloadForm();
}

function generatePagi(selectTab,limit){
	$('#perPage').val(limit);
	$('#selectTab').val(selectTab);
	reloadForm();
}

function sortDataBox(searchFieldValue,searchField){
	//$("#search_grid_box").val('');
	//$("#search_grid_box").hide();
	//$("#grid_search_button").hide();
	//$(".sort_by_list").hide();
	if(searchField==''){
		$("#"+searchFieldValue+"_sort_by").show();
	}else{
		
		if(searchField=='status'){
			$('#status').val(searchFieldValue);
		}else{
			$('#searchByField').val(searchField);
			$('#searchByFieldValue').val(searchFieldValue);
		}
		
		reloadForm();
	}
}

function reloadForm(){
	$('#frmSort').submit();
}

function pageRedirect(url){
	window.location=url;
}

function savaData(bk,ty,referer,editBack){
	var options ={success: function(data)
	{
		
		var str = data;
		var isError = str.indexOf("elementError");
		if(isError>0){
			var getData=$.parseJSON(data);
			$.each(getData, function(val, key) {
				$.each(key, function(fieldVal, fieldKey) {
					$.each(fieldKey, function(fieldVal1, fieldKey1) {
						$("#"+fieldVal1).html(fieldKey1);
					});
				});
			}); 
		}else{
			var id=$("#id").val();
			if(id >0){
				alert('Data Updated');
				if(ty=='save'){
					window.location=SITE_URL+editBack+'/'+id;
				}else{
					if(referer=='true'){
						window.location=bk;
					}else{
						window.location=SITE_URL+bk;
					}
					
				}
			}else{
				alert('Data Added');
				id=str;
				if(ty=='save'){
					window.location=SITE_URL+editBack+'/'+id;
				}else{
					if(referer=='true'){
						window.location=bk;
					}else{
						window.location=SITE_URL+bk;
					}
				}
			}
		}
	}};
	$("#frmData").ajaxForm(options).submit();
}

function prev_next(num,type){
	if(type=='prev'){
		num--;
	}if(type=='next'){
		num++;
	}
	var perPage=PAGE_PER_NO;
	var next=num*parseInt(perPage);
	
	generatePagi(num,next);
}

jQuery('#frmBox').on('submit',function(e){
	jQuery('.field_error').html('');
	var id=jQuery('#id').val();			
	var btnVal=jQuery('#btn').val();
	var urlLink=jQuery('#urlLink').val();
	var section=jQuery('#section').val();
	if(urlLink=='updatePasswordProcess'){
		var new_password=jQuery('#new_password').val();
		var confirm_new_password=jQuery('#confirm_new_password').val();
		if(new_password!=confirm_new_password){
			jQuery('#confirm_password_error').html('You must enter the same password twice in order to confirm it.');
			return false;
		}
	}
	jQuery('#btn').val('Please wait...');
	jQuery('#btn').attr('disabled',true);
	jQuery('#model_btn').trigger('click');
	jQuery('#success_msg').html('Please wait...');
	jQuery.ajax({
		url:SITE_URL+urlLink,
		type:'post',
		enctype: 'multipart/form-data',
		data:new FormData(this),
		processData: false,
        contentType: false,
		success:function(result){
			var str = result;
			var isError = str.indexOf("elementError");
			if(isError>0){
				var getData=$.parseJSON(result);
				$.each(getData, function(val, key) {
					$.each(key, function(fieldVal, fieldKey) {
						$.each(fieldKey, function(fieldVal1, fieldKey1) {
							$("#"+fieldVal1).html(fieldKey1);
						});
					});
				}); 
				setTimeout(function(){ $('.model_popup').modal('hide'); }, 1000);
			}else{
				if(id>0){
					jQuery('#success_msg').html(section+' updated successfully');
					if(urlLink=='updatePasswordProcess'){
						jQuery('#frmBox')['0'].reset();
					}
				}else{
					jQuery('#success_msg').html(section+' added successfully');
					//jQuery('#frmBox')['0'].reset();
				}
			}
			jQuery('#btn').val(btnVal);
			jQuery('#btn').attr('disabled',false);
		}
	});
	e.preventDefault();
});
 
 
jQuery('#frmBox1').on('submit',function(e){
	jQuery('.field_error').html('');
	var id=jQuery('#id').val();			
	var btnVal=jQuery('#btn').val();
	var urlLink=jQuery('#urlLink1').val();
	var section=jQuery('#section1').val();
	if(urlLink=='updatePasswordProcess'){
		var new_password=jQuery('#new_password').val();
		var confirm_new_password=jQuery('#confirm_new_password').val();
		if(new_password!=confirm_new_password){
			jQuery('#confirm_password_error').html('You must enter the same password twice in order to confirm it.');
			return false;
		}
	}
	jQuery('#btn').val('Please wait...');
	jQuery('#btn').attr('disabled',true);
	jQuery('#model_btn').trigger('click');
	jQuery('#success_msg').html('Please wait...');
	jQuery.ajax({
		url:SITE_URL+urlLink,
		type:'post',
		enctype: 'multipart/form-data',
		data:new FormData(this),
		processData: false,
        contentType: false,
		success:function(result){
			var str = result;
			var isError = str.indexOf("elementError");
			if(isError>0){
				var getData=$.parseJSON(result);
				$.each(getData, function(val, key) {
					$.each(key, function(fieldVal, fieldKey) {
						$.each(fieldKey, function(fieldVal1, fieldKey1) {
							$("#"+fieldVal1).html(fieldKey1);
						});
					});
				}); 
				setTimeout(function(){ $('.model_popup').modal('hide'); }, 1000);
			}else{
				if(id>0){
					jQuery('#success_msg').html(section+' updated successfully');
					if(urlLink=='updatePasswordProcess'){
						jQuery('#frmBox')['0'].reset();
					}
				}else{
					jQuery('#success_msg').html(section+' added successfully');
					//jQuery('#frmBox')['0'].reset();
				}
			}
			jQuery('#btn').val(btnVal);
			jQuery('#btn').attr('disabled',false);
		}
	});
	e.preventDefault();
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
// 			alert('Data updated');	
// 			jQuery('.field_error').html('');
// 			jQuery('#btnPersonalInfo').html('Save');
// 	        jQuery('#btnPersonalInfo').attr('disabled',false);	
// 			//jQuery('.step_box').hide();
// 			//jQuery('.step2').show();
// 			//jQuery('html, body').animate({ scrollTop: $('.step2').offset().top }, 500); 
// 		}
// 	});
// 	e.preventDefault();
// }); 

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
	messages:{
		email: {
			pattern:'Please enter a valid email address.',
		}
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
			console.log(result);
			var getData = $.parseJSON(result);
			if(getData.status == 1){
				alert('Data updated');	
				jQuery('.field_error').html('');
				jQuery('#btnPersonalInfo').html('Save');
				jQuery('#btnPersonalInfo').attr('disabled',false);
			}else if(getData.status == 0){
				jQuery('.field_error').html('');
				jQuery('#btnPersonalInfo').html('Save');
				jQuery('#btnPersonalInfo').attr('disabled',false);
			  $("#pan_noError").html(getData.msg);
			}else{
				jQuery('.field_error').html('');
				jQuery('#btnPersonalInfo').html('Save');
				jQuery('#btnPersonalInfo').attr('disabled',false);
			  $("#pan_noError").html("Invalid Pan pattern");
			}
			
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
// 			alert('Data updated');	
// 			jQuery('.field_error').html('');
// 			jQuery('#btnAddress').html('Save');
// 	        jQuery('#btnAddress').attr('disabled',false);	
// 			//jQuery('.step_box').hide();
// 			//jQuery('.step3').show();
// 			//jQuery('html, body').animate({ scrollTop: $('.step3').offset().top }, 500); 
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
			alert('Data updated');	
			jQuery('.field_error').html('');
			jQuery('#btnAddress').html('Save');
	        jQuery('#btnAddress').attr('disabled',false);
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
// 			alert('Data updated');	
// 			jQuery('.field_error').html('');
// 			jQuery('#btnBankDetails').html('Save');
// 	        jQuery('#btnBankDetails').attr('disabled',false);	
// 			//jQuery('.step_box').hide();
// 			//jQuery('.step4').show();
// 			//jQuery('html, body').animate({ scrollTop: $('.step3').offset().top }, 500); 
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
    jQuery(".validation_message_success").html("");
    jQuery(".validation_message_error").html("");
    jQuery("#btnBankDetails").html("Please wait...");
    jQuery("#btnBankDetails").attr("disabled", true);
    jQuery.ajax({
      url: SITE_URL + "submitBankDetails",
      type: "post",
      data: jQuery("#frmBankDetails").serialize(),
      success: function (result) {
        alert('Data updated');	
		jQuery('.field_error').html('');
		jQuery('#btnBankDetails').html('Save');
		jQuery('#btnBankDetails').attr('disabled',false);
      },
    });
  },
});
jQuery('#frmMarketSegments').on('submit',function(e){
	jQuery('.field_error').html('');
	jQuery('#btnMarketSegments').html('Please wait...');
	jQuery('#btnMarketSegments').attr('disabled',true);
	jQuery.ajax({
		url:SITE_URL+'submitMarketSegments',
		type:'post',
		data:jQuery('#frmMarketSegments').serialize(),
		success:function(result){
			alert('Data updated');	
			jQuery('.field_error').html('');
			jQuery('#btnMarketSegments').html('Save');
	        jQuery('#btnMarketSegments').attr('disabled',false);	
			//jQuery('.step_box').hide();
			//jQuery('.step5').show();
			//jQuery('html, body').animate({ scrollTop: $('.step3').offset().top }, 500); 
		}
	});
	e.preventDefault();
});

jQuery('#frmRegulatoryInfo').on('submit',function(e){
	jQuery('.field_error').html('');
	jQuery('#btnRegulatoryInfo').html('Please wait...');
	jQuery('#btnRegulatoryInfo').attr('disabled',true);
	jQuery.ajax({
		url:SITE_URL+'submitRegulatoryInfo',
		type:'post',
		data:jQuery('#frmRegulatoryInfo').serialize(),
		success:function(result){
			alert('Data updated');	
			jQuery('.field_error').html('');
			jQuery('#btnRegulatoryInfo').html('Save');
	        jQuery('#btnRegulatoryInfo').attr('disabled',false);	
			//jQuery('.step_box').hide();
			//jQuery('.step6').show();
			//jQuery('html, body').animate({ scrollTop: $('.step3').offset().top }, 500); 
		}
	});
	e.preventDefault();
});
 //  nomination form 
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
				<div class="form-group">
					<label for="share_of_nominees_${nomnee_count - 1}" style="margin-bottom: 25px;">Share of Nominee(s) </label>
					<div class="input-group">
						<input type="number" class="form-control nominee_share" name="share_of_nominees[]" id="share_of_nominees_${nomnee_count - 1}" >
						<div class="input-group-prepend">
							<div class="input-group-text">%</div>
						</div>
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
  if(val == "1")
    $(".guardian_section").show();
  else  
    $(".guardian_section").hide();
});

function nominationFormErrorPlacement(error, element) {
  // console.log(error, element);
  // console.log($(this));
  // console.log($(element));
  console.log($(element[0]));
 // $(element).closest("div").append(error);
  $(element[0]).closest("div").append(error);
  // if (
  //   element.attr("name") == "nominee_identification[]" 
  // ) {
  //   element.closest("div").append(error);
  // } else {
  // }
}

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
    jQuery("#nominationform").html("Please wait...");
    jQuery("#nominationform").attr("disabled", true);
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
		 alert('Data updated');
		jQuery('.field_error').html('');
		jQuery('.error').html('');
		jQuery('#nominationform').html('Save');
		jQuery('#nominationform').attr('disabled',false);	
      },
    });
  },
});

jQuery('#frmDisclosures').on('submit',function(e){
	jQuery('.field_error').html('');
	jQuery('#btnDisclosures').html('Please wait...');
	jQuery('#btnDisclosures').attr('disabled',true);
	jQuery.ajax({
		url:SITE_URL+'submitDisclosures',
		type:'post',
		data:jQuery('#frmDisclosures').serialize(),
		success:function(result){
			alert('Data updated');	
			jQuery('.field_error').html('');
			jQuery('#btnDisclosures').html('Save');
	        jQuery('#btnDisclosures').attr('disabled',false);	
			//jQuery('.step_box').hide();
			//jQuery('.step7').show();
			//jQuery('html, body').animate({ scrollTop: $('.step3').offset().top }, 500);*/
			//window.location.href=SITE_URL+'thank_you'	
		}
	});
	e.preventDefault();
});

jQuery('#frmKYCDocuments').on('submit',function(e){
	jQuery('.field_error').html('');
	jQuery('#btnKYCDocuments').html('Please wait...');
	jQuery('#btnKYCDocuments').attr('disabled',true);
	jQuery.ajax({
		url:SITE_URL+'submitKYCDocuments',
		type:'post',
		data:new FormData(this),
		processData: false,
		contentType: false,
		success:function(result){
			var isError = result.indexOf("elementError");
			if(isError>0){
				var getData=$.parseJSON(result);
				$.each(getData, function(val, key) {
					$.each(key, function(fieldVal, fieldKey) {
						$.each(fieldKey, function(fieldVal1, fieldKey1) {
							$("#"+fieldVal1).html(fieldKey1);
						});
					});
				}); 
			}else{
				alert('Document uploaded successfully...');	
				/*setTimeout(function(){ 
					window.location.href=SITE_URL+'thank_you'	
				}, 3000);*/

				
			}
			jQuery('#btnKYCDocuments').html('Save');
			jQuery('#btnKYCDocuments').attr('disabled',false);
		}
	});
	e.preventDefault();
});

function step_change(id){
   	jQuery('.step_box').hide();
   	jQuery('.step'+id).show();
    $('html, body').animate({ scrollTop: $('.step'+id).offset().top }, 500); 
   }
   jQuery(document).ready(function() {
       jQuery('#same_as_permanent').change(function() {
           if(jQuery(this).is(":checked")) {
   			jQuery('#correspondence_address_box').show();
			jQuery('#correspondence_address1,#correspondence_address_city,#correspondence_address_country,#correspondence_address_pincode').attr('required','');
           }else{
   			jQuery('#correspondence_address_box').hide();
			jQuery('#correspondence_address1,#correspondence_address_city,#correspondence_address_country,#correspondence_address_pincode').removeAttr('required');
   		}
       });
   	$("#is_politically_exposed").change(function() {
             if($('option:selected', this).val()=='Yes'){
   				jQuery('.politically_exposed_box').show();
   		  }else{
   				jQuery('.politically_exposed_box').hide();
   		  }
   	});
   	$("#is_action_sebi").change(function() {
             if($('option:selected', this).val()=='Yes'){
   				jQuery('#is_action_sebi_box').show();
   		  }else{
   				jQuery('#is_action_sebi_box').hide();
   		  }
   	});
   	jQuery('#is_any_disputes').change(function() {
           if(jQuery(this).is(":checked")) {
   			jQuery('#details_of_disputes_box').show();
           }else{
   			jQuery('#details_of_disputes_box').hide();
   		}
       });
   });
   
   
   	jQuery(window).load(function(){
   		//$(" input").val("");
   		
   		jQuery(".input-effect input").focusout(function(){
   			if(jQuery(this).val() != ""){
   				jQuery(this).addClass("has-content");
   			}else{
   				jQuery(this).removeClass("has-content");
   			}
   		})
   		jQuery("select").focusout(function(){
   			if(jQuery(this).val() != ""){
   				jQuery(this).addClass("has-content");
   			}else{
   				jQuery(this).removeClass("has-content");
   			}
   		});
		
		jQuery("#otp,#dob,#mob,#yob,#correspondence_address_pincode,#permanent_address_pincode").keypress(function (e) {
			if (e.which != 8 && e.which != 0 && (e.which < 48 || e.which > 57)) {
				return false;
			}
		});
   
   	});
	
function showDocUpload(){
	jQuery('#kyc_documents').show();
}	  


function verifyIfsc(){
	jQuery(".validation_message_error").html('');
	var ifsc_code= jQuery("#ifsc_code_i").val();
	var isError='';
	if(ifsc_code == ''){
		jQuery("#ifsc_codeError").html("Please Enter IFSC Code");
		isError='yes';
	}
	if(isError == ''){
		jQuery.ajax({
			url:SITE_URL+'verifyIfscCode',
			type:'post',
			data:"ifscCode="+ifsc_code,
			success:function(result){
				var getData=jQuery.parseJSON(result);
				jQuery.each(getData, function(key, val) {
					if(key == 'error'){
						jQuery("#ifsc_codeError").html(val);
					}else if(key == 'class'){
						jQuery("#ifscBox").removeClass("hide");
						jQuery("#ifsc_code_i").attr('readonly', 'readonly');
						jQuery("#verfyIfsc").addClass('hide');
						jQuery("#chngeIfsc").removeClass('hide');
						jQuery("#btnBankDetails").removeAttr('disabled');
					}else{
						jQuery("#"+key).val(val);
					}
				});
			}
		});
	}
}

function chnageIfsc(){
	jQuery("#bank,#branch,#address").val('');
	jQuery("#ifscBox").addClass("hide");
	jQuery("#ifsc_code_i").removeAttr('readonly');
	jQuery("#verfyIfsc").removeClass('hide');
	jQuery("#chngeIfsc").addClass('hide');
	jQuery("#btnBankDetails").attr('disabled', 'disabled');
}
function verfyDeatils(){ 
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
		$("#chngeBank").hide();
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
			  $("#chngeBank").removeClass("hide");
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
				$("#chngeBank").show();
			}
		},
	  });
	}
  };
  function chngeDeatils(){
	$(".validation_message_error").html("");
	$(".validation_message_success").html("");
	$("#ifsc_code_i").removeAttr("readonly");
	$("#account_number").removeAttr("readonly");
	$("#verfyBank").removeClass("hide");
	$("#chngeBank").addClass("hide");
	$("#btnBankDetails").attr("disabled", "disabled");
  };
function regStatus(reg_status,id){
	if(reg_status != ''){
		jQuery.ajax({
			url:SITE_URL+'updateRegistrationStatus',
			type:'post',
			data:"reg_status="+reg_status+"&id="+id,
			success:function(result){
				$("#userDiv").load(location.href+" #userDiv>","");
			}
		});
	}
}


function sendReminder(id){
	if(id != ''){
		jQuery.ajax({
			url:SITE_URL+'sendReminder',
			type:'post',
			data:"id="+id,
			success:function(result){
				$("#userDiv").load(location.href+" #userDiv>","");
			}
		});
	}
}

function sendKycReminder(id){
	if(id != ''){
		jQuery.ajax({
			url:SITE_URL+'sendKycReminder',
			type:'post',
			data:"id="+id,
			success:function(result){
				$("#userDiv").load(location.href+" #userDiv>","");
			}
		});
	}
}