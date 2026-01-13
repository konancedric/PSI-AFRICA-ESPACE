<table id="data_table" class="table">
    <thead>
        <tr>
            <th>{{ __('Date Add')}}</th>
            <th>{{ __('Nom')}}</th>
            <th>{{ __('Prénom')}}</th>
            <th>{{ __('Email')}}</th>
            <th>{{ __('Services')}}</th>
            <th>{{ __('Contact')}}</th>
            <th>{{ __('Objet')}}</th>
            <th>{{ __('Message')}}</th>
            <th>{{ __('Date Rdv')}}</th>
            <th>{{ __('Update Add')}}</th>
            <th width="30%">{{ __('Action')}}</th>
        </tr>
    </thead>
    <tbody>
        @foreach ($dataRendezVous as $tabRendezVous)
            <tr>
                <td>{{ $tabRendezVous->created_at }}</td>
                <td>{{ $tabRendezVous->nom }}</td>
                <td>{{ $tabRendezVous->prenom }}</td>
                <td>{{ $tabRendezVous->email }}</td>
                <td>
                    @foreach(App\Models\Services::where('id', $tabRendezVous->id_service)->get() as $ServiceSearch)
                        {{ $ServiceSearch->titre }}
                    @endforeach
                </td>
                <td>{{ $tabRendezVous->contact }}</td>
                <td>
                    @if($tabRendezVous->etat == 1)
                        <span class="badge badge-success badge-pill"> {{ $tabRendezVous->objet }}</span>
                    @elseif($tabRendezVous->etat == 0)
                        <span class="badge badge-danger badge-pill"> {{ $tabRendezVous->objet }}</span>
                    @endif
                </td>
                <td>{!! $tabRendezVous->message !!}</td>
                <td>{{ $tabRendezVous->date_rdv }} à {{ $tabRendezVous->heure_rdv }}</td>
                <td>{{ $tabRendezVous->updated_at }}</td>
                <td width="30%">
                    <div class="table-actions">
                        
                        @if($tabRendezVous->etat == 1)
                            @include('admin.rendez-vous.form-disable-rendez-vous')
                        @elseif($tabRendezVous->etat == 0)
                            @include('admin.rendez-vous.form-active-rendez-vous')
                        @endif
                    </div>
                </td>
            </tr>
        @endforeach
    </tbody>
</table>
