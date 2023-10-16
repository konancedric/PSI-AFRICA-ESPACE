<!doctype html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" >
<head>
	<title>@yield('title','') | {{ config('app.name') }} - Système de Gestion des RDV</title>
	<!-- initiate head with meta tags, css and script -->
	@include('include.head')

</head>
<body id="app" >
    <div class="wrapper">
    	<!-- initiate header-->
    	@include('include.header')
    	<div class="page-wrap">
	    	<!-- initiate sidebar-->
	    	@include('include.sidebar')

	    	<div class="main-content">
	    		<!-- yeild contents here -->
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

		    var table = $('#data_table').DataTable({
		        responsive: true,
		        select: true,
		        'aoColumnDefs': [{
		            'bSortable': false,
		            'aTargets': ['nosort']
		        }],
		        dom: "<'row'<'col-sm-2'l><'col-sm-7 text-center'B><'col-sm-3'f>>tipr",
		                buttons: [
		                    {
		                        extend: 'copy',
		                        className: 'btn-sm btn-info', 
		                        header: false,
		                        footer: true,
		                        exportOptions: {
		                            // columns: ':visible'
		                        }
		                    },
		                    {
		                        extend: 'csv',
		                        className: 'btn-sm btn-success',
		                        header: false,
		                        footer: true,
		                        exportOptions: {
		                            // columns: ':visible'
		                        }
		                    },
		                    {
		                        extend: 'excel',
		                        className: 'btn-sm btn-warning',
		                        header: false,
		                        footer: true,
		                        exportOptions: {
		                            // columns: ':visible',
		                        }
		                    },
		                    {
		                        extend: 'pdf',
		                        className: 'btn-sm btn-primary',
		                        header: false,
		                        footer: true,
		                        exportOptions: {
		                            // columns: ':visible'
		                        }
		                    },
		                    {
		                        extend: 'print',
		                        className: 'btn-sm btn-default',
		                        header: true,
		                        footer: false,
		                        orientation: 'landscape',
		                        exportOptions: {
		                            // columns: ':visible',
		                            stripHtml: false
		                        }
		                    }
		                ]
		    
		    });
		    $('#data_table tbody').on( 'click', 'tr', function() {
		        if ( $(this).hasClass('selected') ) {
		            $(this).removeClass('selected');
		        }
		        else {
		            table.$('tr.selected').removeClass('selected');
		            $(this).addClass('selected');
		        }
		    });

		    var table = $('#data_table1').DataTable({
		        responsive: true,
		        select: true,
		        'aoColumnDefs': [{
		            'bSortable': false,
		            'aTargets': ['nosort']
		        }],
		        dom: "<'row'<'col-sm-2'l><'col-sm-7 text-center'B><'col-sm-3'f>>tipr",
		                buttons: [
		                    {
		                        extend: 'copy',
		                        className: 'btn-sm btn-info', 
		                        header: false,
		                        footer: true,
		                        exportOptions: {
		                            // columns: ':visible'
		                        }
		                    },
		                    {
		                        extend: 'csv',
		                        className: 'btn-sm btn-success',
		                        header: false,
		                        footer: true,
		                        exportOptions: {
		                            // columns: ':visible'
		                        }
		                    },
		                    {
		                        extend: 'excel',
		                        className: 'btn-sm btn-warning',
		                        header: false,
		                        footer: true,
		                        exportOptions: {
		                            // columns: ':visible',
		                        }
		                    },
		                    {
		                        extend: 'pdf',
		                        className: 'btn-sm btn-primary',
		                        header: false,
		                        footer: true,
		                        exportOptions: {
		                            // columns: ':visible'
		                        }
		                    },
		                    {
		                        extend: 'print',
		                        className: 'btn-sm btn-default',
		                        header: true,
		                        footer: false,
		                        orientation: 'landscape',
		                        exportOptions: {
		                            // columns: ':visible',
		                            stripHtml: false
		                        }
		                    }
		                ]
		    
		    });
		    $('#data_table1 tbody').on( 'click', 'tr', function() {
		        if ( $(this).hasClass('selected') ) {
		            $(this).removeClass('selected');
		        }
		        else {
		            table.$('tr.selected').removeClass('selected');
		            $(this).addClass('selected');
		        }
		    });
		    var table = $('#data_table2').DataTable({
		        responsive: true,
		        select: true,
		        'aoColumnDefs': [{
		            'bSortable': false,
		            'aTargets': ['nosort']
		        }],
		        dom: "<'row'<'col-sm-2'l><'col-sm-7 text-center'B><'col-sm-3'f>>tipr",
		                buttons: [
		                    {
		                        extend: 'copy',
		                        className: 'btn-sm btn-info', 
		                        header: false,
		                        footer: true,
		                        exportOptions: {
		                            // columns: ':visible'
		                        }
		                    },
		                    {
		                        extend: 'csv',
		                        className: 'btn-sm btn-success',
		                        header: false,
		                        footer: true,
		                        exportOptions: {
		                            // columns: ':visible'
		                        }
		                    },
		                    {
		                        extend: 'excel',
		                        className: 'btn-sm btn-warning',
		                        header: false,
		                        footer: true,
		                        exportOptions: {
		                            // columns: ':visible',
		                        }
		                    },
		                    {
		                        extend: 'pdf',
		                        className: 'btn-sm btn-primary',
		                        header: false,
		                        footer: true,
		                        exportOptions: {
		                            // columns: ':visible'
		                        }
		                    },
		                    {
		                        extend: 'print',
		                        className: 'btn-sm btn-default',
		                        header: true,
		                        footer: false,
		                        orientation: 'landscape',
		                        exportOptions: {
		                            // columns: ':visible',
		                            stripHtml: false
		                        }
		                    }
		                ]
		    
		    });
		    $('#data_table2 tbody').on( 'click', 'tr', function() {
		        if ( $(this).hasClass('selected') ) {
		            $(this).removeClass('selected');
		        }
		        else {
		            table.$('tr.selected').removeClass('selected');
		            $(this).addClass('selected');
		        }
		    });
		  })
	</script>
	<script>
		$(document).ready(function(){
		  $('[data-toggle="tooltip"]').tooltip();   
		});
	</script>
</body>
</html>