
$(document).ready(function() {
	if($('.flot-line').length > 0){
		$(function () {
			var data_load_avg = [], totalPoints = 20;
			var updateInterval = 500;
			var options = {
				series: { 
					shadowSize: 0 
				},
				yaxis: { 
					min: 0, 
					max: 100 
				},
				xaxis: { 
					show: false 
				}
			};
			while (data_load_avg.length < totalPoints) {
				data_load_avg.push(0);
			}
			
			function updateData() {
				if (data_load_avg.length > 0) {
					data_load_avg = data_load_avg.slice(1);
				}
				var url = '<?php echo curl::base(); ?>index.php/admin/core/load_avg';
				if (data_load_avg.length < totalPoints) {
					jQuery.ajax(url, {
						dataType: 'text',
						type: 'POST',
						success: function(data) {
							if (data_load_avg.length >= totalPoints)
								data_load_avg = data_load_avg.slice(1);
							
							data_load_avg.push(data);
							
							var res = [];
							for (var i = 0; i < data_load_avg.length; i++)
								res.push([i, data_load_avg[i]])
							update(data,res);
							setTimeout(updateData, updateInterval);
							
						},
						error: function() {
							
							//$.cresenity.message('error','Error while contacting server, please try again');
						}
					});
					
					
					
				}
				
			}

			


			
			var initial_data = [];
			for (var i = 0; i < totalPoints; i++) {
				initial_data.push([i, 0]);
			}
			for (var i = 0; i < totalPoints; i++) {
				initial_data.push([i, 0]);
			}
			var plot = $.plot($(".flot-line"), [ {
				label: "CPU at %",
				data: initial_data,
				lines: {show: false, fill:true},
				points: {show: false},
				color: '#fd6e58' 
			}], options);

			function update(percent,res) {
				
				
				
				plot.setData([ {
					label: "CPU at "+percent+"%",
					data: res,
					lines: {show: true, fill:true},
					points: {show: false},
					color: '#fd6e58' 
				}]);
				plot.draw();

				
			}

			// This resize bind fixes live-data resize bug in jquery.flot.resize.min
			$(window).resize(function(){
				if($(".flot-line").is(":visible")){
					plot.resize();
					plot.setupGrid();
					plot.draw();
				}
			});

			updateData();
		});
	}
});