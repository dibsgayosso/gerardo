<?php $this->load->view("partial/header"); ?>
<div id="status"><?php echo lang('common_wait');?> <?php echo img(array('src' => base_url().'assets/img/ajax-loader.gif')); ?></div>

<div class="panel panel-piluku">
	<div class="panel-body">
	   <h4 id="title"><?php echo lang('sales_please_swipe_credit_card_on_machine');?></h4>
	</div>
</div>

<form id="formCheckout" method="post" action="<?php echo site_url('sales/start_cc_processing_square_terminal'); ?>">
</form>
<?php $this->load->view("partial/footer"); ?>

<script>
var counter = 0;

$(document).ready(function()
{
	function check_processing_status()
	{
		if (counter < 10)
		{
			counter++;
		
			$.getJSON(<?php echo json_encode(site_url('sales/check_processing_status'));?>,function(json)
			{
				if (json.url)
				{		
					window.location = json.url;
				}
				else
				{
					check_processing_status();
				}
			}).error(function(jqXHR, textStatus, errorThrown) 
			{
				check_processing_status();
			});
		}
	}
	
	$("#formCheckout").ajaxSubmit({
		error: check_processing_status,
		success: check_processing_status
	});	
});
</script>