/* [ ---- Gebo Admin Panel - wizard ---- ] */

	$(document).ready(function() {
		//* simple wizard
//		gebo_wizard.simple();
		//* wizard with validation
		gebo_wizard.validation();
		//* add step numbers to titles
		gebo_wizard.steps_nb();
	});

	gebo_wizard = {
		simple: function(){
			$('#simple_wizard').stepy({
				titleClick	: true,
				nextLabel:      'Next <i class="glyphicon glyphicon-chevron-right"></i>',
				backLabel:      '<i class="glyphicon glyphicon-chevron-left"></i> Back'
			});
		},
		validation: function(){
			$('#validate_wizard').stepy({
				nextLabel:      'Siguiente <i class="glyphicon glyphicon-chevron-right"></i>',
				backLabel:      '<i class="glyphicon glyphicon-chevron-left"></i> Atras',
				block		: true,
				errorImage	: true,
				titleClick	: true,
				validate	: true
			});
			stepy_validation = $('#validate_wizard').validate({
				onfocusout: false,
				errorClass: 'error',
				validClass: 'valid',
				errorPlacement: function(error, element) {
					error.appendTo( element.closest("div.controls") );
				},
				highlight: function(element) {
					$(element).closest("div.form-group").addClass("error f_error");
					var thisStep = $(element).closest('form').prev('ul').find('.current-step');
					thisStep.addClass('error-image');
				},
				unhighlight: function(element) {
					$(element).closest("div.form-group").removeClass("error f_error");
					if(!$(element).closest('form').find('div.error').length) {
						var thisStep = $(element).closest('form').prev('ul').find('.current-step');
						thisStep.removeClass('error-image');
					};
				},
                                errorPlacement: function(error, element) {
                                    $(element).closest('div').append(error);
                                },
				rules: {
                                        'a_cod_alma'      :{
						required	: true,
						minlength	: 4,
                                                maxlength	: 4
					},
                                        'a_cod_estanteria'      :{
						required	: true,
						minlength	: 4,
                                                maxlength	: 4
					},
                                        'a_mod_estanteria'      :{
						required	: false,
						minlength	: 4,
                                                maxlength	: 4
					},
                                        'a_c_desde'             :{
						required	: true,
						minlength	: 4,
                                                maxlength	: 4
					},
                                        'a_c_hasta'             :{
						required	: true,
						minlength	: 4,
                                                maxlength	: 4
					},
                                        'a_nivel'               :{
						required	: true,
						minlength	: 2,
                                                maxlength	: 2
					}
				}, messages: {
                                        'a_cod_alma'      : { required:  'Código de Almacen es obligatorio' },
					'a_cod_estanteria'      : { required:  'Código de Estanteria es obligatorio' },
					'a_c_desde'             : { required:  'Coordenada inicial requerida' },
					'a_c_hasta'             : { required:  'Coordenada final requerida' },
					'a_nivel'		: { required:  'Cantidad de niveles requerido' }
				},
				ignore				: ':hidden'
			});
		},
		//* add numbers to step titles
		steps_nb: function(){
			$('.stepy-titles').each(function(){
				$(this).children('li').each(function(index){
					var myIndex = index + 1
					$(this).append('<span class="stepNb">'+myIndex+'</span>');
				})
			})
		}
	};