<script type="text/javascript">
    $(document).ready(function() {

        var table = $('#data_tableTete2ListeByElection<?=$tabElections->id?>').DataTable({
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
        $('#data_tableTete2ListeByElection<?=$tabElections->id?> tbody').on( 'click', 'tr', function() {
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
<table id="data_tableTete2ListeByElection<?=$tabElections->id?>" class="table">
    <thead>
        <tr>
            <th>{{ __('Nom & Prénom')}}</th>
            <th>{{ __('Matricule')}}</th>
            <th>{{ __('Grade')}}</th>
            <th>{{ __('Catégorie')}}</th>
            <th>{{ __('Contact')}}</th>
            <th>{{ __('Photo')}}</th>
        </tr>
    </thead>
    <tbody>
        <?php
            $tabElecteur = explode('~', $tabElections->tete_liste);
            for($i = 0; $i < count($tabElecteur); $i++)
            {
                foreach(App\Models\User::where('id', $tabElecteur[$i])->get() as $tabUsers)
                {
        ?>
             <tr>
                <td>
                    @if($tabUsers->etat == 1)
                        <span class="badge badge-success badge-pill"> {{ $tabUsers->name }}</span>
                    @elseif($tabUsers->etat == 0)
                        <span class="badge badge-danger badge-pill"> {{ $tabUsers->name }}</span>
                    @endif
                </td>
                <td>{{ $tabUsers->matricule }}</td>
                <td>
                    @foreach(App\Models\Grades::where('id', $tabUsers->id_grade)->get() as $GradeSearch)
                        {{ $GradeSearch->libelle }}
                    @endforeach
                </td>
                <td>
                    @foreach(App\Models\Categories::where('id', $tabUsers->id_categorie)->get() as $CategorieSearch)
                        {{ $CategorieSearch->libelle }}
                    @endforeach
                </td>
                <td>{{ $tabUsers->contact }}</td>
                <td>
                    <img src="upload/users/{{$tabUsers->photo_user }}"  width="80%"/>
                </td>
            </tr>
        <?php
                }
            }
        ?>
    </tbody>
</table>