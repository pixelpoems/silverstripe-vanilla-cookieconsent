import Chart from 'chart.js/auto';

document.addEventListener('DOMContentLoaded', () => {

    initPieChart('canvas#acceptRejectRateChart');
    initPieChart('canvas#acceptRejectRateChart--locale');

    initBarChart('canvas#acceptRejectByCategoryChart');
    initBarChart('canvas#acceptRejectByCategoryChart--locale');

    function initPieChart(selector) {
        // Initialize the pie chart
        const pieChartCanvas = document.querySelector(selector);
        if (!pieChartCanvas) return;

        // Canvas Größe explizit auf eine vernünftige Größe setzen
        pieChartCanvas.style.width = '350px';
        pieChartCanvas.style.height = '350px';
        pieChartCanvas.width = 350;
        pieChartCanvas.height = 350;

        // console.log('Pie Chart Canvas size set to:', pieChartCanvas.style.width, 'x', pieChartCanvas.style.height);

        const pieChart = new Chart(pieChartCanvas, {
            type: 'pie',
            data: {
                labels: ['Accepted', 'Partly', 'Rejected'],
                datasets: [{
                    label: 'Cookie Consent Rate',
                    data: [pieChartCanvas.dataset.accepted, pieChartCanvas.dataset.partly, pieChartCanvas.dataset.rejected],
                    backgroundColor: ['#4CAF50', '#FF9800', '#F44336'],
                }]
            },
            options: {
                responsive: false, // Deaktiviert damit unsere feste Größe verwendet wird
                maintainAspectRatio: false,
                plugins: {
                    legend: {
                        position: 'bottom',
                    },
                    title: {
                        display: false,
                        text: 'Cookie Consent Acceptance Rate'
                    }
                }
            }
        });
    }

    function initBarChart(selector) {
        const barChartCanvas = document.querySelector(selector);
        if (!barChartCanvas) return;


        // Canvas Größe explizit auf eine vernünftige Größe setzen
        barChartCanvas.style.width = '600px';
        barChartCanvas.style.height = '350px';
        barChartCanvas.width = 600;
        barChartCanvas.height = 350;

        // Categories sind im JSON Format im data Attribut gegeben
        let categories = JSON.parse(barChartCanvas.dataset.categories);

        // Separate Arrays für Labels und Daten
        let categoryLabels = [];
        let acceptedData = [];
        let rejectedData = [];

        // Daten aus categories extrahieren
        categories.forEach(category => {
            categoryLabels.push(category.Title);
            acceptedData.push(category.Accepts);
            rejectedData.push(category.Rejects);
        });

        // Debug: Daten validieren
        if (categoryLabels.length === 0) {
            console.error('Keine Kategorie-Labels gefunden!');
            return;
        }

        if (acceptedData.every(val => val === 0) && rejectedData.every(val => val === 0)) {
            console.warn('Alle Werte sind 0!');
        }

        const barChart = new Chart(barChartCanvas, {
            type: 'bar',
            data: {
                labels: categoryLabels, // Nur die Kategorie-Namen als Labels
                datasets: [
                    {
                        label: 'Accepted',
                        data: acceptedData,
                        backgroundColor: '#4CAF50',
                        borderColor: '#4CAF50',
                        borderWidth: 1
                    },
                    {
                        label: 'Rejected',
                        data: rejectedData,
                        backgroundColor: '#F44336',
                        borderColor: '#F44336',
                        borderWidth: 1
                    }
                ]
            },
            options: {
                responsive: false, // Deaktiviert damit unsere feste Größe verwendet wird
                maintainAspectRatio: false,
                plugins: {
                    legend: {
                        display: true,
                        position: 'bottom',
                    }
                },
                scales: {
                    x: {
                        stacked: false
                    },
                    y: {
                        beginAtZero: true,
                        stacked: false
                    }
                },
                interaction: {
                    mode: 'index',
                    intersect: false,
                }
            }
        });
    }


});
