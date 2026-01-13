<table id="data_table" class="table">
    <thead>
        <tr>
            <th>{{ __('Date Add')}}</th>
            <th>{{ __('N° D.')}}</th>
            <th>{{ __('Nom')}}</th>
            <th>{{ __('Prénom')}}</th>
            <th>{{ __('Contact')}}</th>
            <th>{{ __('Email')}}</th>
            <th>{{ __('Date Sejour')}}</th>
            <th>{{ __('Doc Interess')}}</th>
            <th>{{ __('Update Add')}}</th>
            <th width="30%">{{ __('Action')}}</th>
        </tr>
    </thead>
    <tbody>
        @foreach ($dataDocumentsVoyage as $tabDocumentsVoyage)
            <tr>
                <td>{{ $tabDocumentsVoyage->created_at }}</td>
                <td>
                    @if($tabDocumentsVoyage->etat == 1)
                        <span class="badge badge-success badge-pill"> {{ $tabDocumentsVoyage->numero_demande }}</span>
                    @elseif($tabDocumentsVoyage->etat == 0)
                        <span class="badge badge-danger badge-pill"> {{ $tabDocumentsVoyage->numero_demande }}</span>
                    @endif
                </td>
                <td>{{ $tabDocumentsVoyage->nom }}</td>
                <td>{{ $tabDocumentsVoyage->prenom }}</td>
                <td>{{ $tabDocumentsVoyage->contact }}</td>
                <td>{{ $tabDocumentsVoyage->email }}</td>
                <td>{{ $tabDocumentsVoyage->date_sejour_debut }} - {{ $tabDocumentsVoyage->date_sejour_fin }}</td>
                <td>
                    <?php
                        $tabDoc = explode('~', $tabDocumentsVoyage->documents_interess);
                        for($ii = 0; $ii < count($tabDoc); $ii++)
                        {
                    ?>
                        <span class="badge badge-dark badge-pill mt-2"> {{ $tabDoc[$ii] }}</span>
                    <?php } ?>
                </td>
                <td>{{ $tabDocumentsVoyage->updated_at }}</td>
                <td width="30%">
                    <div class="table-actions">
                        
                        @if($tabDocumentsVoyage->etat == 1)
                            @include('admin.documents-voyage.form-disable-documents-voyage')
                        @elseif($tabDocumentsVoyage->etat == 0)
                            @include('admin.documents-voyage.form-active-documents-voyage')
                        @endif
                    </div>
                </td>
            </tr>
        @endforeach
    </tbody>
</table>
