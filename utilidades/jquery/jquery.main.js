$(function(){
    //original field values
    var field_values = {
            //id        :  value
    		'dni'  : 'numero de documento',
            'username'  : 'usuario',
            'password'  : 'password',
            'cpassword' : 'password',
            'firstname'  : 'nombre',
            'lastname'  : 'apellido',
            'email'  : 'email',
    };


    //inputfocus
    $('input#dni').inputfocus({ value: field_values['dni'] });
    $('input#username').inputfocus({ value: field_values['username'] });
    $('input#password').inputfocus({ value: field_values['password'] });
    $('input#cpassword').inputfocus({ value: field_values['cpassword'] }); 
    $('input#lastname').inputfocus({ value: field_values['lastname'] });
    $('input#firstname').inputfocus({ value: field_values['firstname'] });
    $('input#email').inputfocus({ value: field_values['email'] }); 




    //reset progress bar
    $('#progress').css('width','0');
    $('#progress_text').html('0% Completo');

    //first_step
    $('form').submit(function(){ return false; });
    $('#submit_first').click(function(){
        //remove classes
        $('#first_step input').removeClass('error').removeClass('valid');

        var emailPattern = /^[a-zA-Z0-9._-]+@[a-zA-Z0-9.-]+\.[a-zA-Z]{2,4}$/;  
        var fields = $('#first_step input[type=text]');
        var error = 0;
        fields.each(function(){
            var value = $(this).val();
            if( value.length<1 || value==field_values[$(this).attr('id')] || ( $(this).attr('id')=='email' && !emailPattern.test(value) ) ) {
                $(this).addClass('error');
                $(this).effect("shake", { times:3 }, 50);
                
                error++;
            } else {
                $(this).addClass('valid');
            }
        });
        
        if(!error) {
           
                //update progress bar
                $('#progress_text').html('50% Completo');
                $('#progress').css('width','170px');
                
                //slide steps
                $('#first_step').slideUp();
                $('#second_step').slideDown();     
        } else return false;
    });


    $('#submit_second').click(function(){
        //remove classes
        $('#second_step input').removeClass('error').removeClass('valid');

        //ckeck if inputs aren't empty
        var fields = $('#second_step input[type=text], #second_step input[type=password]');
        var error = 0;
        fields.each(function(){
            var value = $(this).val();
            if( value.length<4 || value==field_values[$(this).attr('id')] ) {
                $(this).addClass('error');
                $(this).effect("shake", { times:3 }, 50);
                
                error++;
            } else {
                $(this).addClass('valid');
            }
        });        
        

        if(!error) {
        	  if( $('#password').val() != $('#cpassword').val() ) {
                  $('#second_step input[type=password]').each(function(){
                      $(this).removeClass('valid').addClass('error');
                      $(this).effect("shake", { times:3 }, 50);
                  });
                  
                  return false;
        	  } else {   
                //update progress bar
                $('#progress_text').html('100% Completo');
                $('#progress').css('width','339px');
                
                //prepare the fourth step
                var fields = new Array(
                	($('#tipoDni').val()==1?"DNI":"LC"),
                	$('#dni').val(),
                    $('#username').val(),
                    $('#password').val(),
                    $('#email').val(),
                    $('#firstname').val() + ' ' + $('#lastname').val(),
                    $('#sex').val(),
                    $('#datepicker').val()
                );
                var tr = $('#third_step tr');
                tr.each(function(){
                    //alert( fields[$(this).index()] )
                    $(this).children('td:nth-child(2)').html(fields[$(this).index()]);
                });
                
                //slide steps
                $('#second_step').slideUp();
                $('#third_step').slideDown();     
        	  }
        } else return false;

    });
	$( "#datepicker" ).datepicker({
		showOn: "button",
		buttonImage: "gui/images/iconos/calendar.gif",
		buttonImageOnly: true
	});

    $('#submit_third').click(function(){
        alert('enviar la infooooo');
    	var fields = "tipoDni="+$('#tipoDni').val()+
            		"&dni="+$('#dni').val()+
            		"&username="+$('#username').val()+
            		"&password="+$('#password').val()+
            		"&email="+$('#email').val()+
            		"&firstname="+$('#firstname').val()+
            		"&lastname="+$('#lastname').val()+
            		"&sex="+$('#sex').val()+
            		"&fechaNacimiento="+$('#datepicker').val()+"";                       
    	$.ajax({
    		type:	"POST",
    		url: 	"registrarse",
    		data: 	fields,
    		success: function(data){
    			var lista = $.parseJSON(data);
    		}
    	});
    });

});