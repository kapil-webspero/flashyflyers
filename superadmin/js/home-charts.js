$(document).ready(function() {
	
	$.ajax({
		type: "POST",
		url: "../ajax/admin-charts.php",
		data: "viewType=salesByCategory",
		success: function(regResponse) {
			regResponse = JSON.parse(regResponse);	
			var canvas = document.getElementById("doughnut-chart");
			var ctx = canvas.getContext('2d');
			var lbl = [];
			var perVal = [];
			var coloR = [];
	
			var dynamicColors = function() {
				var r = Math.floor(Math.random() * 255);
				var g = Math.floor(Math.random() * 255);
				var b = Math.floor(Math.random() * 255);
				return "rgb(" + r + "," + g + "," + b + ")";
			};
			
			$.each(regResponse, function (index, value) {
				lbl.push(index);
				perVal.push(value);
				coloR.push(dynamicColors());
			});

			var data = { labels: lbl, datasets: [{
					backgroundColor: coloR,
					data: perVal
				}]
			};
			 
			var options = {
				responsive: true,
				legend: {
					display: false
				},
				elements: {
					arc: {
						borderWidth: 0
					}
				},
				cutoutPercentage: 30,
				tooltips: {
					callbacks: {
						label: function(tooltipItem, data) {
			
							var dataset = data.datasets[tooltipItem.datasetIndex];
			
							var tooltipLabel = data.labels[tooltipItem.index];
							var total = dataset.data.reduce(function(previousValue, currentValue, currentIndex, array) {
								return previousValue + currentValue;
							});
			
							var currentValue = dataset.data[tooltipItem.index];
							console.log(currentValue);
			
							var precentage = Math.floor(((currentValue / total) * 100) + 0.5);
							var finalVal = tooltipLabel + ' ' + precentage + "%";
							return finalVal;
						}
					}
				},
				//custom legends
				legendCallback: function(chart) {
					var text = [];
					text.push('<ul class="' + chart.id + '-legend">');
					var data = chart.data;
					var datasets = data.datasets;
					var labels = data.labels;
					if (datasets.length) {
						for (var i = 0; i < datasets[0].data.length; ++i) {
							text.push('<li><span style="background-color:' + datasets[0].backgroundColor[i] + '"></span>');
							if (labels[i]) {
								// calculate percentage
								var total = datasets[0].data.reduce(function(previousValue, currentValue, currentIndex, array) {
									return previousValue + currentValue;
								});
								var currentValue = datasets[0].data[i];
								var precentage = Math.floor(((currentValue / total) * 100) + 0.5);
			
								text.push(labels[i] + ' (' + precentage + '%)');
							}
							text.push('</li>');
						}
					}
					text.push('</ul>');
					return text.join('');
				}
			};
			
			var dognutChart = new Chart(ctx, {
				type: 'doughnut',
				data: data,
				options: options
			});
			
					document.getElementById('chart-legend').innerHTML = dognutChart.generateLegend();
			
			}
	});	
	
	var monthNames = ["January", "February", "March", "April", "May", "June", "July", "August", "September", "October", "November", "December"];
	
	var today = new Date();
	var d;
	var month = [];

	for(var i = 6; i >= 0; i -= 1) {
	  	d = new Date(today.getFullYear(), today.getMonth() - i, 1);
	  	month.push(monthNames[d.getMonth()]);
	}
	
	$.ajax({
		type: "POST",
		url: "../ajax/admin-charts.php",
		data: "viewType=salesCategoryData&catId=31",
		success: function(regResponse) {
			console.log(regResponse);
			regResponse = JSON.parse(regResponse);

			//Sales graph
			new Chart(document.getElementById("sales-graph-31"), {
				type: 'line',
				data: {
					labels: month,
					datasets: [{
						data: regResponse,
						borderColor: "#3e95cd",
						label: "Birthday Flyers sales",
						fill: false
					}]
				}
			});	
		}
	});	
	
	$.ajax({
		type: "POST",
		url: "../ajax/admin-charts.php",
		data: "viewType=salesCategoryData&catId=24",
		success: function(regResponse) {
			regResponse = JSON.parse(regResponse);

			//Animated flyer graph
			new Chart(document.getElementById("sales-graph-24"), {
				type: 'line',
				data: {
					labels: month,
					datasets: [{
						data: regResponse,
						borderColor: "#f9360f",
						label: "Church Flyers sales",
						fill: false
					}]
				}
			});
		}
	});	
	
	
	$.ajax({
		type: "POST",
		url: "../ajax/admin-charts.php",
		data: "viewType=salesCategoryData&catId=25",
		success: function(regResponse) {
			regResponse = JSON.parse(regResponse);
			
			//Motion flyer graph
			new Chart(document.getElementById("sales-graph-25"), {
				type: 'line',
				data: {
					labels: month,
					datasets: [{
						data: regResponse,
						borderColor: "#038c30",
						label: "Dj Booking Flyers sales",
						fill: false
					}]
				}
			});
		}
	});		
	
	$.ajax({
		type: "POST",
		url: "../ajax/admin-charts.php",
		data: "viewType=salesCategoryData&catId=26",
		success: function(regResponse) {
			regResponse = JSON.parse(regResponse);
			
			//Motion flyer graph
			new Chart(document.getElementById("sales-graph-26"), {
				type: 'line',
				data: {
					labels: month,
					datasets: [{
						data: regResponse,
						borderColor: "#2d59a9",
						label: "Night Club Flyers sales",
						fill: false
					}]
				}
			});
		}
	});		
	
	$.ajax({
		type: "POST",
		url: "../ajax/admin-charts.php",
		data: "viewType=salesCategoryDataOther",
		success: function(regResponse) {
			regResponse = JSON.parse(regResponse);
			
			//Other Products graph
			new Chart(document.getElementById("sales-graph-other"), {
				type: 'line',
				data: {
					labels: month,
					datasets: [{
						data: regResponse,
						borderColor: "#7c68dd",
						label: "All product sales",
						fill: false
					}]
				}
			});			
		}
	});
	
});