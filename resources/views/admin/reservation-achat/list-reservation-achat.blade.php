<table id="data_table" class="table">
    <thead>
        <tr>
            <th>{{ __('Date Add')}}</th>
            <th>{{ __('N° D.')}}</th>
            <th>{{ __('Nom')}}</th>
            <th>{{ __('Prénom')}}</th>
            <th>{{ __('Contact')}}</th>
            <th>{{ __('Type')}}</th>
            <th>{{ __('Date Sejour')}}</th>
            <th>{{ __('Email')}}</th>
            <th>{{ __('Pays Depart')}}</th>
            <th>{{ __('Pays Destination')}}</th>
            <th>{{ __('E. 0-2')}}</th>
            <th>{{ __('E. 2-11')}}</th>
            <th>{{ __('Update Add')}}</th>
            <th width="30%">{{ __('Action')}}</th>
        </tr>
    </thead>
    <tbody>
        @foreach ($dataReservationAchat as $tabReservationAchat)
            <tr>
                <td>{{ $tabReservationAchat->created_at }}</td>
                <td>
                    @if($tabReservationAchat->etat == 1)
                        <span class="badge badge-success badge-pill"> {{ $tabReservationAchat->numero_demande }}</span>
                    @elseif($tabReservationAchat->etat == 0)
                        <span class="badge badge-danger badge-pill"> {{ $tabReservationAchat->numero_demande }}</span>
                    @endif
                </td>
                <td>{{ $tabReservationAchat->nom }}</td>
                <td>{{ $tabReservationAchat->prenom }}</td>
                <td>{{ $tabReservationAchat->contact }}</td>
                <td>{{ $tabReservationAchat->type_voyage }}</td>
                <td>{{ $tabReservationAchat->date_voyage_aller }} - {{ $tabReservationAchat->date_voyage_retour }}</td>
                <td>{{ $tabReservationAchat->email }}</td>
                <td>{{ $tabReservationAchat->pays_depart }}</td>
                <td>{{ $tabReservationAchat->pays_destination }}</td>
                <td>{{ $tabReservationAchat->enfant_0_2 }}</td>
                <td>{{ $tabReservationAchat->enfant_2_11 }}</td>
                <td>{{ $tabReservationAchat->updated_at }}</td>
                <td width="30%">
                    <div class="table-actions">
                        
                        @if($tabReservationAchat->etat == 1)
                            @include('admin.reservation-achat.form-disable-reservation-achat')
                        @elseif($tabReservationAchat->etat == 0)
                            @include('admin.reservation-achat.form-active-reservation-achat')
                        @endif
                    </div>
                </td>
            </tr>
        @endforeach
    </tbody>
</table>
