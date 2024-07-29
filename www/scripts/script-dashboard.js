$(document).ready(function() {
    $.ajax({
        url: 'action/get-dashboard-data.php',
        method: 'GET',
        dataType: 'json',
        success: function(data) {
            createChart1(data.top5_unpaid_invoices.slice(0, 5));
            createChart2(data.top5_amount_owed.slice(0, 5));
            createChart3(data.unpaid_vs_paid);
            createChart4(data.monthly_relances);
        },
        error: function(xhr, status, error) {
            console.error('Erreur lors de la récupération des données du tableau de bord:', error);
        }
    });

    function createChart1(data) {        
        const labels = data.map(item => `${item.numeros_parma} - ${item.nom_client}`);
        const values = data.map(item => item.num_unpaid_invoices);
        const ctx = document.getElementById('chart1').getContext('2d');
        new Chart(ctx, {
            type: 'bar',
            data: {
                labels: labels,
                datasets: [{
                    label: 'Nombre de factures impayées',
                    data: values,
                    backgroundColor: 'rgba(75, 192, 192, 0.2)',
                    borderColor: 'rgba(75, 192, 192, 1)',
                    borderWidth: 1
                }]
            },
            options: {
                aspectRatio: 3, // Rendre le graphique moins large
                scales: {
                    y: {
                        beginAtZero: true,
                        ticks: {
                            stepSize: 1, // Ajouter cette ligne pour n'afficher que les nombres entiers
                            font: {
                                size: 12,
                                family: 'Arial'
                            },
                            color: 'var(--text-color)'
                        }
                    },
                    x: {
                        ticks: {
                            font: {
                                size: 12,
                                family: 'Arial'
                            },
                            color: 'var(--text-color)'
                        }
                    }
                },
                plugins: {
                    legend: {
                        labels: {
                            font: {
                                size: 12,
                                family: 'Arial'
                            },
                            color: 'var(--text-color)'
                        }
                    },
                    title: {
                        display: true,
                        text: 'Top 5 des clients par nombre de factures impayées',
                        font: {
                            size: 16,
                            family: 'Arial'
                        },
                        color: 'var(--text-color)'
                    }
                }
            }
        });
    }

    function createChart2(data) {
        const labels = data.map(item => `${item.numeros_parma} - ${item.nom_client}`);
        const values = data.map(item => item.total_amount_owed);
        const ctx = document.getElementById('chart2').getContext('2d');
        new Chart(ctx, {
            type: 'bar',
            data: {
                labels: labels,
                datasets: [{
                    label: 'Montant total dû',
                    data: values,
                    backgroundColor: 'rgba(153, 102, 255, 0.2)',
                    borderColor: 'rgba(153, 102, 255, 1)',
                    borderWidth: 1
                }]
            },
            options: {
                aspectRatio: 3, // Rendre le graphique moins large
                scales: {
                    y: {
                        beginAtZero: true,
                        ticks: {
                            callback: function(value, index, values) {
                                return '€' + value.toFixed(2);
                            },
                            font: {
                                size: 12,
                                family: 'Arial'
                            },
                            color: 'var(--text-color)'
                        }
                    },
                    x: {
                        ticks: {
                            font: {
                                size: 12,
                                family: 'Arial'
                            },
                            color: 'var(--text-color)'
                        }
                    }
                },
                plugins: {
                    legend: {
                        labels: {
                            font: {
                                size: 12,
                                family: 'Arial'
                            },
                            color: 'var(--text-color)'
                        }
                    },
                    title: {
                        display: true,
                        text: 'Top 5 des clients par montant total dû',
                        font: {
                            size: 16,
                            family: 'Arial'
                        },
                        color: 'var(--text-color)'
                    }
                }
            }
        });
    }

    function createChart3(data) {
        const ctx = document.getElementById('chart3').getContext('2d');
        new Chart(ctx, {
            type: 'pie',
            data: {
                labels: ['Clients avec factures impayées', 'Clients sans factures impayées'],
                datasets: [{
                    data: [data.clients_with_unpaid, data.clients_no_unpaid],
                    backgroundColor: ['rgba(255, 99, 132, 0.2)', 'rgba(54, 162, 235, 0.2)'],
                    borderColor: ['rgba(255, 99, 132, 1)', 'rgba(54, 162, 235, 1)'],
                    borderWidth: 1
                }]
            },
            options: {
                aspectRatio: 3, // Rendre le graphique moins large
                plugins: {
                    legend: {
                        labels: {
                            font: {
                                size: 12,
                                family: 'Arial'
                            },
                            color: 'var(--text-color)'
                        }
                    },
                    title: {
                        display: true,
                        text: 'Répartition des clients avec et sans factures impayées',
                        font: {
                            size: 16,
                            family: 'Arial'
                        },
                        color: 'var(--text-color)'
                    }
                }
            }
        });
    }

    function createChart4(data) {
        const ctx = document.getElementById('chart4').getContext('2d');
        new Chart(ctx, {
            type: 'line',
            data: {
                labels: data.months,
                datasets: [{
                    label: 'Nombre de relances programmées',
                    data: data.relances,
                    backgroundColor: 'rgba(255, 206, 86, 0.2)',
                    borderColor: 'rgba(255, 206, 86, 1)',
                    borderWidth: 1,
                    fill: true
                }]
            },
            options: {
                aspectRatio: 3, // Rendre le graphique moins large
                scales: {
                    y: {
                        beginAtZero: true,
                        ticks: {
                            stepSize: 2,
                            font: {
                                size: 12,
                                family: 'Arial'
                            },
                            color: 'var(--text-color)'
                        }
                    },
                    x: {
                        ticks: {
                            font: {
                                size: 12,
                                family: 'Arial'
                            },
                            color: 'var(--text-color)'
                        }
                    }
                },
                plugins: {
                    legend: {
                        labels: {
                            font: {
                                size: 12,
                                family: 'Arial'
                            },
                            color: 'var(--text-color)'
                        }
                    },
                    title: {
                        display: true,
                        text: 'Nombre de relances programmées par mois',
                        font: {
                            size: 16,
                            family: 'Arial'
                        },
                        color: 'var(--text-color)'
                    }
                }
            }
        });
    }
});
