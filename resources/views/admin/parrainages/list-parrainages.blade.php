<table id="data_table" class="table">
    <thead>
        <tr>
            <th>{{ __('Date Add')}}</th>
            <th>{{ __('Nom')}}</th>
            <th>{{ __('Prénom')}}</th>
            <th>{{ __('Email')}}</th>
            <th>{{ __('Contact')}}</th>

            <th>{{ __('Nom & Prenom Parrain')}}</th>
            <th>{{ __('Contact Parrain')}}</th>
            <th>{{ __('Email Parrain')}}</th>
            <th>{{ __('Information')}}</th>
            <th>{{ __('Update Add')}}</th>
            <th width="30%">{{ __('Action')}}</th>
        </tr>
    </thead>
    <tbody>
        @foreach ($dataParrainages as $tabParrainages)
            <tr>
                <td>{{ $tabParrainages->created_at }}</td>
                <td>{{ $tabParrainages->nom }}</td>
                <td>{{ $tabParrainages->prenom }}</td>
                <td>{{ $tabParrainages->email }}</td>
                <td>{{ $tabParrainages->contact }}</td>

                <td>
                    @if($tabParrainages->etat == 1)
                        <span class="badge badge-success badge-pill"> {{ $tabParrainages->nom_parrain }} {{ $tabParrainages->prenom_parrain }}</span>
                    @elseif($tabParrainages->etat == 0)
                        <span class="badge badge-danger badge-pill"> {{ $tabParrainages->nom_parrain }} {{ $tabParrainages->prenom_parrain }}</span>
                    @endif
                </td>
                <td>{{ $tabParrainages->contact_parrain }}</td>
                <td>{{ $tabParrainages->email_parrain }}</td>

                <td>{!! $tabParrainages->message !!}</td>
                <td>{{ $tabParrainages->updated_at }}</td>
                <td width="30%">
                    <div class="table-actions">
                        
                        @if($tabParrainages->etat == 1)
                            @include('admin.parrainages.form-disable-parrainages')
                        @elseif($tabParrainages->etat == 0)
                            @include('admin.parrainages.form-active-parrainages')
                        @endif
                    </div>
                </td>
            </tr>
        @endforeach
    </tbody>
</table>
