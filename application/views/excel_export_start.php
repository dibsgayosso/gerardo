<?php $this->load->view("partial/header"); ?>

<div id="status"><?php echo lang('common_wait');?> <?php echo img(array('src' => base_url().'assets/img/ajax-loader.gif')); ?></div>

<div class="panel panel-piluku">
	<div class="panel-body">
	   <h4 id="title"><?php echo lang('common_exporting');?>...</h4>
	</div>
</div>

<script>
	var counter = 0;
	
	$(document).ready(function()
	{
		
		function get_recent_export_file_url()
		{
			if (counter < 10)
			{
				counter++;
				
				$.getJSON(<?php echo json_encode($status_check_url);?>,function(json)
				{
					if (json.url)
					{				
						window.location = json.url;
						$("#title").text(<?php echo json_encode(lang('common_done')); ?>);
						$("#status").remove();
					}
					else
					{
						get_recent_export_file_url();
					}
				}).error(function(jqXHR, textStatus, errorThrown) 
				{
					get_recent_export_file_url();
				});
			}
		}
		
		
		$.get(<?php echo json_encode($export_url)?>,get_recent_export_file_url).error(get_recent_export_file_url);
		
	});
</script>
<?php $this->load->view('partial/footer'); ?>