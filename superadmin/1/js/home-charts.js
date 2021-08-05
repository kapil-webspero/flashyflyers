var canvas = document.getElementById("doughnut-chart");
var ctx = canvas.getContext('2d');

var data = {
    labels: ["Flyers", "Animated flyers", "Facebook covers", "Laptop skins", "CD Covers"],
    datasets: [{
        backgroundColor: ["#8d6658", "#fc4b6c", "#1e88e5", "#ffb22b", "#7c277d"],
        data: [40, 30, 10, 10, 10]
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

//Sales graph
new Chart(document.getElementById("sales-graph"), {
    type: 'line',
    data: {
        labels: ["January", "Feburary", "March", "April", "May", "June", "July"],
        datasets: [{
            data: [86, 114, 106, 106, 200, 90, 160],
            borderColor: "#3e95cd",
            label: "Flyer sales",
            fill: false
        }]
    }
});

//Animated flyer graph
new Chart(document.getElementById("animated-flyer-graph"), {
    type: 'line',
    data: {
        labels: ["January", "Feburary", "March", "April", "May", "June", "July"],
        datasets: [{
            data: [46, 214, 86, 146, 200, 110, 120],
            borderColor: "#f9360f",
            label: "Animated flyer sales",
            fill: false
        }]
    }
});

//Motion flyer graph
new Chart(document.getElementById("motion-flyer-graph"), {
    type: 'line',
    data: {
        labels: ["January", "Feburary", "March", "April", "May", "June", "July"],
        datasets: [{
            data: [92, 122, 45, 79, 176, 111, 23],
            borderColor: "#038c30",
            label: "Animated flyer sales",
            fill: false
        }]
    }
});

//Other Products graph
new Chart(document.getElementById("other-products-graph"), {
    type: 'line',
    data: {
        labels: ["January", "Feburary", "March", "April", "May", "June", "July"],
        datasets: [{
            data: [64, 79, 122, 90, 200, 45, 150],
            borderColor: "#7c68dd",
            label: "Other product sales",
            fill: false
        }]
    }
});