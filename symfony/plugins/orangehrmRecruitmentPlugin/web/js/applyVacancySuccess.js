$(document).ready(function() {
	$("#uploaded").hide();
    if(candidateId > 0) {
        $(".formInputText").attr('disabled', 'disabled');
        $(".formInput").attr('disabled', 'disabled');
        $(".contactNo").attr('disabled', 'disabled');
        $(".keyWords").attr('disabled', 'disabled');
        $("#cvHelp").hide();
        $("#uploaded").show();
        $("#btnSave").hide();
    }	
	var isCollapse = false;
	$("#txtArea").attr('disabled', 'disabled');
	$("#txtArea").hide();

	$('#extend').click(function(){
		if(!isCollapse){
			$("#txtArea").show();
			isCollapse = true;
			$('#extend').text('[-]');
		} else {
			$("#txtArea").hide();
			isCollapse = false;
			$('#extend').text('[+]');
		}
	});
        
	$('#btnSave').click(function() {
           
		if(isValidForm()){ 
			$('#addCandidate_vacancyList').val(vacancyId);
			$('#addCandidate_keyWords.inputFormatHint').val('');
			$('form#frmAddCandidate').attr({
				action:linkForApplyVacancy+"?id="+vacancyId
			});
			$('form#frmAddCandidate').submit();
		}
	});
        

    $('#backLink').click(function(){
        window.location.replace(linkForViewJobs);
    });
	if ($("#addCandidate_keyWords").val() == '') {
		$("#addCandidate_keyWords").val(lang_commaSeparated).addClass("inputFormatHint");
	}

	$("#addCandidate_keyWords").one('focus', function() {

		if ($(this).hasClass("inputFormatHint")) {
			$(this).val("");
			$(this).removeClass("inputFormatHint");
		}
	});

	
});

function isValidForm(){
	
	$.validator.addMethod("uniqueEmail", function(value, element, params) {
		var isUnique = true;
		var currentCandidate;
		var candidateCount = candidateList.length;
		
		for (var j=0; j < candidateCount; j++) {
			if(candidateId == candidateList[j].candidateId){
				currentCandidate = j;
			}
		}
		
		candidateEmail = $.trim($('#addCandidate_email').val()).toLowerCase();
		for (var i=0; i < candidateCount; i++) {
			if(candidateEmail != '') {
				if(candidateList[i].email) {
					email = candidateList[i].email.toLowerCase();
					if (candidateEmail == email) {
						isUnique = false
						break;
					}
				}
			}
		}
		
		if(currentCandidate != null){
			if(candidateList[currentCandidate].email != null) {
				if(candidateEmail == candidateList[currentCandidate].email.toLowerCase()){
					isUnique = true;
				}
			}
		}
		return isUnique;
	});
	
	$.validator.addMethod("uniquePhone", function(value, element, params) {
		var isUniquePhone = true;
		var currentCandidate;
		var candidateCount = candidateList.length;
		for (var j=0; j < candidateCount; j++) {
			if(candidateId == candidateList[j].candidateId){
				currentCandidate = j;
			}
		}
		
		candidatePhone = $.trim($('#addCandidate_contactNo').val()).toLowerCase();
		for (var i=0; i < candidateCount; i++) {
			if(candidatePhone != '') {
				if(candidateList[i].contactNumber) {
					phone = candidateList[i].contactNumber.toLowerCase();
					if (candidatePhone == phone) {
						isUniquePhone = false
						break;
					}
				}
			}
		}
		
		if(currentCandidate != null){
			if(candidateList[currentCandidate].contactNumber != null) {
				if(candidatePhone == candidateList[currentCandidate].contactNumber.toLowerCase()){
					isUniquePhone = true;
				}
			}
		}
		
		return isUniquePhone;
	});

	var validator = $("#frmAddCandidate").validate({

		rules: {
			'addCandidate[firstName]' : {
				required:true,
				maxlength:30
			},

			'addCandidate[middleName]' : {
				maxlength:30
			},

			'addCandidate[lastName]' : {
				required:true,
				maxlength:30
			},
			'addCandidate[email]' : {
				required:true,
				email:true,
				uniqueEmail: true,
				maxlength:30

			},

			'addCandidate[contactNo]': {
				phone: true,
				uniquePhone: true,
				maxlength:30
			},

			'addCandidate[resume]' : {
				required:true
			},

			'addCandidate[keyWords]': {
				maxlength:250
			}
		},
		messages: {
			'addCandidate[firstName]' : {
				required: lang_firstNameRequired,
				maxlength: lang_tooLargeInput
			},

			'addCandidate[middleName]' : {
				maxlength: lang_tooLargeInput
			},


			'addCandidate[lastName]' : {
				required: lang_lastNameRequired,
				maxlength: lang_tooLargeInput
			},

			
            'addCandidate[email]' : {
				required: lang_emailRequired,
				email: lang_validEmail,
				uniqueEmail: lang_emailExistmsg,
				maxlength: lang_tooLargeInput
			},
            
            'addCandidate[contactNo]': {
				phone: lang_validPhoneNo,
				uniquePhone: lang_emailExistmsg,
				maxlength:lang_tooLargeInput
			},

			'addCandidate[resume]' : {
				required:lang_resumeRequired
			},

			'addCandidate[keyWords]': {
				maxlength:lang_noMoreThan250
			}
		}

	});
	return true;
}