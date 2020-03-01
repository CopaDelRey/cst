(function($) {
	function unpublishComment (e) {
		e.preventDefault();
		if (id = $(this).attr('data-id')) {
			var data = {
				'action': 'unpublishcomment',
				'id': id,
			};
			$.post('postdata.php', data, function(response) {
				console.log(response);
				let $block = $('.comment-'+response).find('.wrap');
				$block.addClass('alert-warning').removeClass('bg-light');
				let addHtml = '<div class="marker-unpublished mb-1"><em>Unpublished</em></div>';
				$block.prepend(addHtml);
				$block = $block.find('.buttons');
				$block.empty();
				addHtml = '<btn class="btn btn-success btn-publish" data-id="'+response+'">Publish</btn>';
				$block.append(addHtml);
				$block.find('.btn-publish').click(publishComment);
			});
		}
	}

	function publishComment (e) {
		e.preventDefault();
		if (id = $(this).attr('data-id')) {
			var data = {
				'action': 'publishcomment',
				'id': id,
			};
			$.post('postdata.php', data, function(response) {
				console.log(response);
				let $block = $('.comment-'+response).find('.wrap');
				$block.addClass('bg-light').removeClass('alert-warning');
				$block.find('.marker-unpublished').remove();
				$block = $block.find('.buttons');
				$block.empty();
				let addHtml = '<btn class="btn btn-warning btn-unpublish" data-id="'+response+'">Unpublish</btn>';
				$block.append(addHtml);
				$block.find('.btn-unpublish').click(unpublishComment);
			});
		}
	}

	$(document).ready(function() {
		// Unpublish comment buttons
		$('.btn-unpublish').click(unpublishComment);

		// Publish comment buttons
		$('.btn-publish').click(publishComment);

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
					'action': 'getcommentsadmin',
					'page': pagenum,
				};
				$.post('postdata.php', data, function(response) {
					if (jQuery.isArray(response)) {
						$commentswrap = $(document).find('.row');
						$commentswrap.empty();
						jQuery.each(response, function( i, val ) {
							let html = '';
							if (val['state'] > 0) {
								html = '<div class="comment comment-'+val['id']+' col col-12 mb-2"><div class="wrap bg-light shadow-sm rounded"><div class="buttons"><btn class="btn btn-warning btn-unpublish" data-id='+val['id']+'>Unpublish</btn></div>';
							} else {
								html = '<div class="comment comment-'+val['id']+' col col-12 mb-2"><div class="wrap alert-warning shadow-sm rounded"><div class="marker-unpublished mb-1"><em>Unpublished</em></div><div class="buttons"><btn class="btn btn-success btn-publish" data-id='+val['id']+'>Publish</btn></div>';
							}
							html += '<div><span class="comment-author">';
							html += val['author'];
							if (val['link'] && val['linktitle']) {
								html += '</span> <em>commented on<em> <a href="/';
								html += val['link'];
								html += '">';
								html += val['linktitle'];
								html += '</a></div><div class="comment-body">'
							} else {
								html += '</span> <em>in frontpage</em></div><div class="comment-body">';
							}
							html += val['text'];
							html += '</div></div></div>';
							$commentswrap.append(html);
						});
						$commentswrap.find('.btn-unpublish').click(unpublishComment);
						$commentswrap.find('.btn-publish').click(publishComment);
					}
				});
			}
		});
	});
})( jQuery );
