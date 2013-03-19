/**
 * @copyright Copyright (C) 2013 Mark Dexter. All rights reserved.
 * @license GNU General Public License version 2 or later; see LICENSE.txt
 */
(function ($) {
	$.JQPLOTBarchart = function(containerId, jsonurl) {
		var drawjqChart = function(url, tag) {
			$.ajax({
				url : url,
				type : "GET",
				dataType : "json",
				success : onDataReceived
			});

			function onDataReceived(series) {
				var chartData = series[0];
				var chartTicks = series[1];
				var chartLabels = series[2];
				var title = series[3];
				var plot2 = $.jqplot(containerId, chartData, {
					title : title,
					stackSeries : true,
					// The "seriesDefaults" option is an options object that
					// will
					// be applied to all series in the chart.
					seriesDefaults : {
						renderer : $.jqplot.BarRenderer,
						rendererOptions : {
							fillToZero : true,
							barDirection : 'horizontal',
							barWidth: 10,
							barMargin: 1,
							barPadding: 1,
						},
						pointLabels: {show: false}
					},
					// Custom labels for the series are specified with the
					// "label"
					// option on the series option. Here a series option object
					// is specified for each series.
					series : chartLabels,
					// Show the legend and put it outside the grid, but inside
					// the
					// plot container, shrinking the grid to accomodate the
					// legend.
					// A value of "outside" would not shrink the grid and allow
					// the legend to overflow the container.
					legend : {
						show : true,
						placement : 'outsideGrid'
					},
					axes : {
						// Use a category axis on the x axis and use our custom
						// ticks.
						xaxis : {padMin: 0, pad: 1.05},
						yaxis : {
							renderer : $.jqplot.CategoryAxisRenderer,
							ticks : chartTicks
						}
					}
				});
			}

		};
		drawjqChart(jsonurl, containerId);
	}
})(jQuery);
