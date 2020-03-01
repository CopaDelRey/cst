(function($) {
	$(document).ready(function() {
		// Comment submission button click
		$('#new-comment-form').on('submit',function(e) {e.preventDefault();});

		$('#submit-comment-btn').click(function(){
			var ok = true;
			var name = $(this).parent().find('#input-name').val();
			if (!name) {
				$(this).parent().find('#input-name').addClass('is-invalid');
				$(this).parent().find('#input-name-help').addClass('d-none');
				ok = false;
			}
			var comment = $(this).parent().find('#input-text').val();
			if (!comment) {
				$(this).parent().find('#input-text').addClass('is-invalid');
				$(this).parent().find('#input-text-help').addClass('d-none');
				ok = false;
			}
			if (ok) {
				var formdata = new FormData();
				formdata.append('key',1);
				formdata.append('action','newcomment');
				formdata.append('name',encodeURIComponent(name));
				formdata.append('comment',encodeURIComponent(comment));
				var tmp = $(this).parent().data('id');
				if (tmp) {formdata.append('parent',tmp);}
				

				//for (var key of formdata.entries()) {
				//	console.log(key[0] + ', ' + key[1]);
				//}

				$.ajax({
					type: 'POST',
					url: 'postdata.php',
					data: formdata,
					contentType: false,
					processData: false,
					dataType: 'json',
					success: function(response){
						//console.log(response);
						$nc = $('.new-comment');
						$nc.remove('.alert');
						$nc.find('#input-name').val('');
						$nc.find('#input-text').val('');
						if (jQuery.isArray(response)) {
							$nc.prepend('<div class="alert alert-success alert-dismissible fade show" role="alert"><strong>Success!</strong> Your comment is successfully added to the page.<button type="button" class="close" data-dismiss="alert"><span>&times;</span></button></div>');
							$commentswrap = $('.comments').find('.row');
							$commentswrap.empty();
							jQuery.each(response, function( i, val ) {
								html = '<div class="comment col col-12 mb-2"><div class="wrap bg-light shadow-sm rounded"><div><span class="comment-author">';
								html += val['author'];
								if (tmp) {html += '</span></div><div class="comment-body">';}
								else {
									if (val['link'] && val['linktitle']) {
										html += '</span> <em>commented on<em> <a href="/';
										html += val['link'];
										html += '">';
										html += val['linktitle'];
										html += '</a></div><div class="comment-body">'
									} else {
										html += '</span> <em>in frontpage</em></div><div class="comment-body">';
									}
								}
								html += val['text'];
								html += '</div></div></div>';
								$commentswrap.append(html);
							});
						} else if (response == 1) {
							$nc.prepend('<div class="alert alert-info alert-dismissible fade show" role="alert"><strong>Info: </strong> Your comment is successfully added to the site and will be reviewed by our administrators.<button type="button" class="close" data-dismiss="alert"><span>&times;</span></button></div>');
						}
					}
				});
			} // if (ok)
		})

		// Remove the red border from inputs on focus
		$('#input-name').focus(function() {
			$(this).removeClass('is-invalid');
			$(this).parent().find('#input-name-help').removeClass('d-none');
		});

		$('#input-text').focus(function() {
			$(this).removeClass('is-invalid');
			$(this).parent().find('#input-text-help').removeClass('d-none');
		});

		// Pagination links
		$('.page-link').click(function(e){
			e.preventDefault();
			if (pagenum = $(this).attr('data-page')) {
				pagelinks = $(this).parent().parent().find('.page-item');
				pagelinks.each(function() {$(this).removeClass('disabled');});
				
				if (pagenum == 1) {
					$(this).parent().parent().find('#nav-item-prev').addClass('disabled');
					$(this).parent().parent().find('.page-item-1').addClass('disabled');
				} else if (pagenum == $(this).parent().parent().find('#nav-item-last').find('.page-link').attr('data-page')) {
					$(this).parent().parent().find('#nav-item-next').addClass('disabled');
				}
				$(this).parent().parent().find('.page-item-'+pagenum).addClass('disabled');
				$(this).parent().parent().find('#nav-item-prev').find('.page-link').attr('data-page',(pagenum-1));
				$(this).parent().parent().find('#nav-item-next').find('.page-link').attr('data-page',(pagenum-(-1)));

				var data = {
					'action': 'getpage',
					'page': pagenum,
				};
				var tmp = $(document).find('#new-comment-form').data('id');
				if (tmp) {data['parent'] = tmp;}
				$.post('postdata.php', data, function(response) {
						if (jQuery.isArray(response)) {
							$commentswrap = $('.comments').find('.row');
							$commentswrap.empty();
							jQuery.each(response, function( i, val ) {
								html = '<div class="comment col col-12 mb-2"><div class="wrap bg-light shadow-sm rounded"><div><span class="comment-author">';
								html += val['author'];
								if (tmp) {html += '</span></div><div class="comment-body">';}
								else {
									if (val['link'] && val['linktitle']) {
										html += '</span> <em>commented on<em> <a href="/';
										html += val['link'];
										html += '">';
										html += val['linktitle'];
										html += '</a></div><div class="comment-body">'
									} else {
										html += '</span> <em>in frontpage</em></div><div class="comment-body">';
									}
								}
								html += val['text'];
								html += '</div></div></div>';
								$commentswrap.append(html);
							});
						}
				});
			}
		});
	});
})( jQuery );
