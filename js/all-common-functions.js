function getUserAlbumDownload(album_id){
	if(album_id != ''){
		$('#download_album_link_'+album_id).html('<img src="images/progress-loader.gif" />');
		$.ajax({
			url: "ajax-php/ajax-user-album-download.php",
			type: "POST",
			data: "album_id="+album_id,
			dataType: "json",
			async:false,
			success: function(resp){				
				 if(resp.ErrorCode == 0){
					$('#download_album_link_'+album_id).html('').hide();
					$('#download_zip_link_'+album_id).html(resp.Content).show();			
				 }
			}
		});
	}
	return false;
}

function check_all_check_box(curr_elem){ 
	var total_check_box=$('input[name="chk_list[]"]').length;
	
	if(curr_elem.attr('checked')){
		$("input[name^='chk_list']").each(function() {
			$(this).attr('checked',true);
			
		});
	}else{
		$("input[name^='chk_list']").each(function() {
			$(this).attr('checked',false);
			
		});	
	}
}

function click_check_box(){
	var total_check_box=$('input[name="chk_list[]"]').length;
	var checked = 0;
	
	$("input[name^='chk_list']").each(function() {
		if($(this).attr('checked')){
			checked++;
		}
	});
	
	if(total_check_box == checked){
		$('#select_all_checkbox').attr('checked',true);
	}else{
		$('#select_all_checkbox').attr('checked',false);
	}	
}

function downloadSelectedAlbums(){
	var checked = 0;
	
	$("input[name^='chk_list']").each(function() {
		if($(this).attr('checked')){
			checked++;
		}
	});
	
	if(checked == 0){
		alert('Please select at least one checkbox to download that album');
		return false;
	}else{
		return true;
	}
}