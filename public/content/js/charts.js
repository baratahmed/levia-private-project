const line = new Chart($('#line-chart'), {
    type: 'line',
    data: {
        datasets: [{
            data: [10, 20, 30],
        }],
    
        // These labels appear in the legend and in the tooltips when hovering different arcs
        labels: [
            'Red',
            'Yellow',
            'Blue'
        ]
    }
});

const pie = new Chart($('#pie-chart'), {
    type: 'pie',
    data: {
        datasets: [{
            data: [10, 20, 30],
        }],
    
        // These labels appear in the legend and in the tooltips when hovering different arcs
        labels: [
            'Red',
            'Yellow',
            'Blue'
        ]
    }
});