<!doctype html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <title>@yield('title','') | {{ config('app.name') }} - Système de Gestion Integré</title>
    <!-- initiate head with meta tags, css and script -->
    @include('include.head')
    
    <style>
        /* Styles pour améliorer l'affichage des tables */
        .dataTables_wrapper .dataTables_length,
        .dataTables_wrapper .dataTables_filter,
        .dataTables_wrapper .dataTables_info,
        .dataTables_wrapper .dataTables_processing,
        .dataTables_wrapper .dataTables_paginate {
            margin: 10px 0;
        }
        
        .dt-buttons {
            margin-bottom: 15px;
        }
        
        .dt-button {
            margin-right: 5px !important;
        }
        
        /* Amélioration de l'apparence des tables */
        table.dataTable {
            border-collapse: collapse !important;
            width: 100% !important;
        }
        
        table.dataTable thead th {
            background-color: #f8f9fa;
            border: 1px solid #dee2e6;
            font-weight: bold;
            font-size: 12px;
            text-align: center;
        }
        
        table.dataTable tbody td {
            border: 1px solid #dee2e6;
            padding: 6px;
            font-size: 11px;
            vertical-align: middle;
        }
        
        /* Styles spécifiques pour les réservations */
        .reservations-container {
            overflow-x: auto;
        }
        
        .status-badge {
            padding: 2px 8px;
            border-radius: 12px;
            color: white;
            font-size: 10px;
            font-weight: bold;
        }
        
        .status-confirmed {
            background-color: #28a745;
        }
        
        .status-pending {
            background-color: #ffc107;
            color: #000;
        }
        
        .status-cancelled {
            background-color: #dc3545;
        }
        
        .amount-cell {
            font-weight: bold;
            color: #007bff;
        }
        
        .action-buttons {
            display: flex;
            gap: 5px;
            justify-content: center;
        }
        
        .action-buttons .btn {
            padding: 2px 6px;
            font-size: 10px;
        }
        
        .product-name {
            font-weight: 600;
            color: #495057;
        }
        
        .reservation-date {
            color: #6c757d;
            font-size: 10px;
        }
    </style>
</head>
<body id="app">
    <div class="wrapper">
        <!-- initiate header-->
        @include('include.header')
        <div class="page-wrap">
            <!-- initiate sidebar-->
            @include('include.sidebar')

            <div class="main-content">
                <!-- yield contents here -->
                @yield('content')
            </div>

            <!-- initiate footer section-->
            @include('include.footer')
        </div>
    </div>

    <!-- initiate scripts-->
    @include('include.script')
    
    <script type="text/javascript">
        $(document).ready(function() {
            
            // Fonction pour initialiser la table des réservations achat
            function initializeReservationsTable() {
                
                // Détruire la table existante si elle existe
                if ($.fn.DataTable.isDataTable('#data_table')) {
                    $('#data_table').DataTable().destroy();
                }

                // Configuration spéciale pour les réservations achat
                var reservationsTable = $('#data_table').DataTable({
                    responsive: true,
                    select: true,
                    processing: true,
                    'aoColumnDefs': [{
                        'bSortable': false,
                        'aTargets': ['nosort']
                    }],
                    order: [[0, 'desc']],
                    pageLength: 25,
                    scrollX: true,
                    autoWidth: false,
                    dom: "<'row'<'col-sm-2'l><'col-sm-7 text-center'B><'col-sm-3'f>>tipr",
                    buttons: [
                        {
                            extend: 'copy',
                            className: 'btn-sm btn-info', 
                            text: '<i class="fa fa-copy"></i> Copy',
                            exportOptions: {
                                columns: ':not(.nosort):not(:last-child)'
                            }
                        },
                        {
                            extend: 'csv',
                            className: 'btn-sm btn-success',
                            text: '<i class="fa fa-file-csv"></i> CSV',
                            header: true,
                            footer: true,
                            filename: function() {
                                var d = new Date();
                                return 'PSI_Africa_Reservations_Achat_' + d.getFullYear() + '-' + 
                                       (d.getMonth() + 1).toString().padStart(2, '0') + '-' + 
                                       d.getDate().toString().padStart(2, '0') + '_' + 
                                       d.getHours().toString().padStart(2, '0') + 'h' + 
                                       d.getMinutes().toString().padStart(2, '0');
                            },
                            exportOptions: {
                                columns: ':not(.nosort):not(:last-child)'
                            }
                        },
                        {
                            extend: 'excel',
                            className: 'btn-sm btn-warning',
                            text: '<i class="fa fa-file-excel"></i> Excel',
                            header: true,
                            footer: true,
                            title: 'PSI AFRICA - RÉSERVATIONS ACHAT',
                            filename: function() {
                                var d = new Date();
                                return 'PSI_Africa_Reservations_Achat_' + d.getFullYear() + '-' + 
                                       (d.getMonth() + 1).toString().padStart(2, '0') + '-' + 
                                       d.getDate().toString().padStart(2, '0') + '_' + 
                                       d.getHours().toString().padStart(2, '0') + 'h' + 
                                       d.getMinutes().toString().padStart(2, '0');
                            },
                            exportOptions: {
                                columns: ':not(.nosort):not(:last-child)'
                            }
                        },
                        {
                            extend: 'pdf',
                            className: 'btn-sm btn-primary',
                            text: '<i class="fa fa-file-pdf"></i> PDF',
                            header: true,
                            footer: true,
                            title: 'PSI AFRICA - RÉSERVATIONS ACHAT',
                            orientation: 'landscape',
                            pageSize: 'A4', // Format A4 au lieu de A3
                            filename: function() {
                                var d = new Date();
                                return 'PSI_Africa_Reservations_Achat_' + d.getFullYear() + '-' + 
                                       (d.getMonth() + 1).toString().padStart(2, '0') + '-' + 
                                       d.getDate().toString().padStart(2, '0') + '_' + 
                                       d.getHours().toString().padStart(2, '0') + 'h' + 
                                       d.getMinutes().toString().padStart(2, '0');
                            },
                            exportOptions: {
                                columns: ':not(.nosort):not(:last-child)'
                            },
                            customize: function(doc) {
                                try {
                                    // Supprimer le titre par défaut
                                    if (doc.content && doc.content.length > 0) {
                                        doc.content.shift();
                                    }
                                    
                                    // En-tête personnalisé PSI AFRICA pour réservations
                                    doc.content.unshift({
                                        text: [
                                            {text: 'PSI AFRICA - SYSTÈME DE GESTION INTÉGRÉ\n', fontSize: 16, bold: true, color: '#ff6b35', alignment: 'center'},
                                            {text: 'FICHE DE SUIVI\n', fontSize: 12, bold: true, alignment: 'center', color: '#333'},
                                            {text: 'Gestion des Clients\n', fontSize: 10, alignment: 'center', italics: true},
                                            {text: 'Généré le: ' + new Date().toLocaleDateString('fr-FR') + ' à ' + new Date().toLocaleTimeString('fr-FR') + '\n\n', fontSize: 9, alignment: 'center', color: '#666'}
                                        ],
                                        margin: [0, 0, 0, 15]
                                    });

                                    // Configuration du tableau pour les réservations en format A4
                                    if (doc.content[1] && doc.content[1].table) {
                                        var table = doc.content[1].table;
                                        
                                        // Largeurs optimisées pour format A4 paysage
                                        var columnCount = table.body[0].length;
                                        
                                        if (columnCount <= 6) {
                                            table.widths = ['16%', '18%', '20%', '20%', '15%', '11%'];
                                        } else if (columnCount <= 8) {
                                            table.widths = ['13%', '15%', '16%', '18%', '12%', '10%', '10%', '6%'];
                                        } else if (columnCount <= 10) {
                                            table.widths = ['10%', '11%', '13%', '15%', '10%', '9%', '9%', '8%', '8%', '7%'];
                                        } else {
                                            // Pour plus de 10 colonnes, ajustement automatique
                                            var baseWidth = Math.floor(100 / columnCount);
                                            table.widths = Array(columnCount).fill(baseWidth + '%');
                                        }
                                        
                                        // Style de l'en-tête
                                        if (table.body && table.body[0]) {
                                            for (var i = 0; i < table.body[0].length; i++) {
                                                table.body[0][i] = {
                                                    text: table.body[0][i],
                                                    fillColor: '#ff6b35',
                                                    color: 'white',
                                                    bold: true,
                                                    fontSize: 7, // Police réduite pour A4
                                                    alignment: 'center',
                                                    margin: [1, 3, 1, 3]
                                                };
                                            }
                                        }
                                        
                                        // Style des données
                                        for (var i = 1; i < table.body.length; i++) {
                                            for (var j = 0; j < table.body[i].length; j++) {
                                                var cellText = table.body[i][j];
                                                
                                                // Traitement spécial pour certaines colonnes (raccourcir pour A4)
                                                if (typeof cellText === 'string') {
                                                    // Limiter plus sévèrement pour format A4
                                                    if (j === 3 && cellText.length > 25) { // Colonne Produit/Service
                                                        cellText = cellText.substring(0, 22) + '...';
                                                    }
                                                    // Limiter les noms de clients
                                                    if (j === 2 && cellText.length > 20) { // Colonne Client
                                                        cellText = cellText.substring(0, 17) + '...';
                                                    }
                                                }
                                                
                                                table.body[i][j] = {
                                                    text: cellText,
                                                    fontSize: 6, // Police plus petite pour A4
                                                    alignment: (j === 2 || j === 3) ? 'left' : 'center', // Client et Produit à gauche
                                                    margin: [1, 1, 1, 1],
                                                    fillColor: i % 2 === 0 ? '#f8f9fa' : null
                                                };
                                                
                                                // Couleurs spéciales pour certaines colonnes
                                                if (j === 6 || j === 5) { // Prix et Total
                                                    table.body[i][j].bold = true;
                                                    table.body[i][j].color = '#007bff';
                                                }
                                                if (j === 7) { // Statut
                                                    table.body[i][j].bold = true;
                                                    if (typeof cellText === 'string') {
                                                        if (cellText.toLowerCase().includes('confirmé') || cellText.toLowerCase().includes('validé')) {
                                                            table.body[i][j].color = '#28a745';
                                                        } else if (cellText.toLowerCase().includes('attente') || cellText.toLowerCase().includes('pending')) {
                                                            table.body[i][j].color = '#ffc107';
                                                        } else if (cellText.toLowerCase().includes('annulé') || cellText.toLowerCase().includes('cancelled')) {
                                                            table.body[i][j].color = '#dc3545';
                                                        }
                                                    }
                                                }
                                                if (j === 8) { // Date Livraison
                                                    table.body[i][j].color = '#6f42c1';
                                                    table.body[i][j].bold = true;
                                                }
                                            }
                                        }
                                        
                                        // Layout du tableau
                                        table.layout = {
                                            hLineWidth: function (i, node) {
                                                return (i === 0 || i === node.table.body.length) ? 1 : 0.5;
                                            },
                                            vLineWidth: function (i, node) {
                                                return 0.5;
                                            },
                                            hLineColor: function (i, node) {
                                                return '#cccccc';
                                            },
                                            vLineColor: function (i, node) {
                                                return '#cccccc';
                                            }
                                        };
                                    }

                                    // Pied de page personnalisé
                                    doc.footer = function(currentPage, pageCount) {
                                        return {
                                            table: {
                                                widths: ['33%', '34%', '33%'],
                                                body: [
                                                    [
                                                        {text: 'PSI AFRICA', fontSize: 7, alignment: 'left', color: '#666', border: [0, 0, 0, 0]},
                                                        {text: 'SUIVI - Page ' + currentPage + '/' + pageCount, fontSize: 7, alignment: 'center', color: '#666', border: [0, 0, 0, 0]},
                                                        {text: 'CONFIDENTIEL', fontSize: 7, alignment: 'right', color: '#ff6b35', bold: true, border: [0, 0, 0, 0]}
                                                    ]
                                                ]
                                            },
                                            margin: [20, 10, 20, 0]
                                        };
                                    };

                                    // Marges optimisées pour format A4
                                    doc.pageMargins = [20, 70, 20, 50];
                                    
                                    // Style par défaut pour A4
                                    doc.defaultStyle = {
                                        fontSize: 6,
                                        lineHeight: 1.1
                                    };

                                    // Configuration pour éviter les coupures
                                    if (doc.content[1] && doc.content[1].table) {
                                        doc.content[1].table.dontBreakRows = true;
                                        doc.content[1].table.keepWithHeaderRows = 1;
                                    }

                                    // Ajouter un résumé en bas si souhaité
                                    doc.content.push({
                                        text: '\n\nDocument généré automatiquement par le système PSI AFRICA',
                                        fontSize: 7,
                                        alignment: 'center',
                                        color: '#999',
                                        margin: [0, 15, 0, 0]
                                    });

                                } catch (error) {
                                    console.error('Erreur lors de la personnalisation du PDF:', error);
                                }
                            }
                        },
                        {
                            extend: 'print',
                            className: 'btn-sm btn-secondary',
                            text: '<i class="fa fa-print"></i> Print',
                            title: 'PSI AFRICA - RÉSERVATIONS ACHAT',
                            orientation: 'landscape',
                            exportOptions: {
                                columns: ':not(.nosort):not(:last-child)'
                            }
                        }
                    ],
                    language: {
                        "processing": "Traitement en cours...",
                        "search": "Rechercher&nbsp;:",
                        "lengthMenu": "Afficher _MENU_ éléments",
                        "info": "Affichage de l'élément _START_ à _END_ sur _TOTAL_ éléments",
                        "infoEmpty": "Affichage de l'élément 0 à 0 sur 0 élément",
                        "infoFiltered": "(filtré de _MAX_ éléments au total)",
                        "paginate": {
                            "first": "Premier",
                            "previous": "Précédent", 
                            "next": "Suivant",
                            "last": "Dernier"
                        },
                        "emptyTable": "Aucune réservation trouvée",
                        "zeroRecords": "Aucune réservation correspondante trouvée"
                    }
                });

                // Gestion de la sélection des lignes
                $('#data_table tbody').on('click', 'tr', function() {
                    if ($(this).hasClass('selected')) {
                        $(this).removeClass('selected');
                    } else {
                        reservationsTable.$('tr.selected').removeClass('selected');
                        $(this).addClass('selected');
                    }
                });

                // Debug pour les boutons d'export
                $('.dt-buttons').on('click', '.btn', function() {
                    var buttonText = $(this).text().trim();
                    console.log('Export button clicked:', buttonText);
                    
                    if (buttonText.includes('PDF')) {
                        $(this).prop('disabled', true);
                        $(this).html('<i class="fa fa-spinner fa-spin"></i> Génération...');
                        
                        setTimeout(function() {
                            $('.btn-primary').prop('disabled', false);
                            $('.btn-primary').html('<i class="fa fa-file-pdf"></i> PDF');
                        }, 3000);
                    }
                });

                console.log('✅ Table des réservations achat initialisée avec succès (Format A4)!');
                return reservationsTable;
            }

            // Initialiser la table au chargement de la page
            var tableInstance = initializeReservationsTable();

            // Gestion des tooltips
            $('[data-toggle="tooltip"]').tooltip({
                container: 'body',
                delay: { "show": 500, "hide": 100 }
            });

            // Gestion responsive
            $(window).on('resize', function() {
                setTimeout(function() {
                    if (tableInstance) {
                        tableInstance.columns.adjust().responsive.recalc();
                    }
                }, 100);
            });

            // Fonction pour recharger la table
            window.reloadReservationsTable = function() {
                if (tableInstance && tableInstance.ajax) {
                    tableInstance.ajax.reload();
                } else {
                    location.reload();
                }
            };

            // Fonction pour exporter manuellement
            window.exportReservations = function(format) {
                if (tableInstance && tableInstance.button) {
                    try {
                        tableInstance.button('.' + format + ':name').trigger();
                        console.log('✅ Export ' + format + ' déclenché');
                    } catch (error) {
                        console.error('❌ Erreur lors de l\'export:', error);
                    }
                }
            };

            // Test de connectivité DataTables
            if (typeof $.fn.DataTable === 'undefined') {
                console.error('❌ DataTables n\'est pas chargé!');
                alert('Erreur: DataTables n\'est pas chargé. Vérifiez vos inclusions de scripts.');
            } else {
                console.log('✅ DataTables est disponible');
            }
        });

        // Fonction de diagnostic pour débugger les réservations
        function diagnosticReservations() {
            console.log('=== DIAGNOSTIC RÉSERVATIONS ACHAT (A4) ===');
            console.log('jQuery disponible:', typeof $ !== 'undefined');
            console.log('DataTables disponible:', typeof $.fn.DataTable !== 'undefined');
            console.log('Table existe:', $('#data_table').length > 0);
            console.log('Table initialisée:', $.fn.DataTable.isDataTable('#data_table'));
            console.log('Boutons visibles:', $('.dt-buttons .btn').length);
            
            if ($.fn.DataTable.isDataTable('#data_table')) {
                var table = $('#data_table').DataTable();
                console.log('Données dans la table:', table.data().count());
                console.log('Boutons PDF:', table.button('.pdf:name').length);
                console.log('Colonnes détectées:', table.columns().header().length);
            }
        }

        // Auto-diagnostic au chargement
        setTimeout(diagnosticReservations, 2000);

        // Fonctions utilitaires supplémentaires
        window.calculateTotalReservations = function() {
            if ($.fn.DataTable.isDataTable('#data_table')) {
                var table = $('#data_table').DataTable();
                var total = 0;
                var count = 0;
                
                table.column(6).data().each(function(value, index) { // Colonne Total
                    if (value && !isNaN(parseFloat(value))) {
                        total += parseFloat(value);
                        count++;
                    }
                });
                
                console.log('Total des réservations:', total.toFixed(2));
                console.log('Nombre de réservations:', count);
                return {total: total, count: count};
            }
        };

        // Fonction pour filtrer par statut
        window.filterByStatus = function(status) {
            if ($.fn.DataTable.isDataTable('#data_table')) {
                var table = $('#data_table').DataTable();
                table.column(7).search(status).draw(); // Colonne Statut
            }
        };
    </script>
</body>
</html>