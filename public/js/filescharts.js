function FilesChart(id, labels, data) {

    var ctx1 = document.getElementById(id).getContext('2d');
    var textdocuments = new Chart(ctx1, {
        type: 'polarArea',
        data: {
                labels: labels,
                datasets: [{
                    label: 'registered',
                    data: data,
                    backgroundColor:  [   'rgba(0, 181, 173, 0.7)',
                        'rgba(251, 189, 8, 0.7)',
                        'rgba(219, 40, 40, 0.7)',
                        'rgba(100, 53, 201, 0.7)',
                        'rgba(33, 133, 208, 0.7)',
                        'rgba(242, 113, 28, 0.7)',
                        'rgba(242, 113, 28, 0.7)'
                    ],
                    borderColor: [
                        'rgba(0, 181, 173, 1)',
                        'rgba(251, 189, 8, 1)',
                        'rgba(219, 40, 40, 1)',
                        'rgba(100, 53, 201, 1)',
                        'rgba(33, 133, 208, 1)',
                        'rgba(242, 113, 28, 1)',
                        'rgba(242, 113, 28, 1)'
                    ],
                    borderWidth: 1
                }]
            }});
    return ctx1;
}