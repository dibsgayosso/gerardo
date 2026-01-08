
<div class="row hidden-print">
	
	<div class="col-md-12">
		
		<div class="panel panel-piluku">
			
			<div class="panel-heading report-options">
				<?php echo $input_report_title; ?>
				<?php if (isset($output_data) && $output_data) { ?>
							<div class="table_buttons pull-right">
								<button type="button" class="btn btn-more expand-collapse" data-toggle="dropdown" aria-expanded="false"><i id="expand-collapse-icon" class="ion-chevron-down"></i></button>
							</div>
				<?php } ?>
			</div>
			<div id="options" class="panel-body" <?php echo (isset($output_data) && $output_data) ? 'style="display:none;"' : ''; ?>>
				
				<form class="form-horizontal form-horizontal-mobiles" id="report_input_form" method="get" action="<?php echo site_url('reports/generate/'.$report); ?>">
					<?php 
					$this->load->helper('view');
					foreach($input_params as $input_param) 
					{
							load_cleaned_view('reports/inputs/'.$input_param['view'],$input_param);
					} 
					?>
					
					
					<div style="display: none;" id="status"><?php echo lang('common_wait');?> <?php echo img(array('src' => base_url().'assets/img/ajax-loader.gif')); ?></div>
					
				</form>
			</div>
		</div>
</div>
</div>
<script>
var counter = 0;			
	
$('#generate_report').click(function(e){
     e.preventDefault();
	 
	 
	 if($("#export_excel_yes").is(':checked'))
	 {
		 
		$("#status").show();
		$("#generate_report").hide();
  		function get_recent_export_file_url()
  		{
  			if (counter < 10)
  			{
				counter++;
				
  				$.getJSON(<?php echo json_encode(site_url('reports/get_recent_export_file_url'));?>,function(json)
  				{
  					if (json.url)
  					{		
						$("#status").hide();
						$("#generate_report").show();
								
  						window.location = json.url;
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
		 
		 
		 $("#report_input_form").ajaxSubmit({
			error: get_recent_export_file_url,
			success: get_recent_export_file_url
	 	});
				 
	 }
	 else
	 {
	     $('#options').slideToggle(function() {
	     	$('#report_input_form').submit();
	     });
	 }
	 
	 
 });

</script>