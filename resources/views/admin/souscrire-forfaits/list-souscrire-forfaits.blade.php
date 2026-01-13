<table id="data_table" class="table">
    <thead>
        <tr>
            <th>{{ __('Date Add')}}</th>
            <th>{{ __('Nom')}}</th>
            <th>{{ __('Prénom')}}</th>
            <th>{{ __('Contact')}}</th>
            <th>{{ __('Whatsapp')}}</th>
            <th>{{ __('Email')}}</th>
            <th>{{ __('Type Forfait')}}</th>
            <th>{{ __('Update Add')}}</th>
            <th width="30%">{{ __('Action')}}</th>
        </tr>
    </thead>
    <tbody>
        @foreach ($dataSouscrireForfaits as $tabSouscrireForfaits)
            <tr>
                <td>{{ $tabSouscrireForfaits->created_at }}</td>
                <td>{{ $tabSouscrireForfaits->nom }}</td>
                <td>{{ $tabSouscrireForfaits->prenom }}</td>
                <td>{{ $tabSouscrireForfaits->email }}</td>
                <td>{{ $tabSouscrireForfaits->contact }}</td>
                <td>{{ $tabSouscrireForfaits->numero_whatsapp }}</td>
                <td>
                    @if($tabSouscrireForfaits->etat == 1)
                        <span class="badge badge-success badge-pill"> 
                            @foreach(App\Models\Forfaits::where('id', $tabSouscrireForfaits->id_type_forfait)->get() as $Forfaitsearch)
                                {{ $Forfaitsearch->titre }}
                            @endforeach
                        </span>
                    @elseif($tabSouscrireForfaits->etat == 0)
                        <span class="badge badge-danger badge-pill">
                            @foreach(App\Models\Forfaits::where('id', $tabSouscrireForfaits->id_type_forfait)->get() as $Forfaitsearch)
                                {{ $Forfaitsearch->titre }}
                            @endforeach
                        </span>
                    @endif
                </td>
                <td>{{ $tabSouscrireForfaits->updated_at }}</td>
                <td width="30%">
                    <div class="table-actions">
                        
                        @if($tabSouscrireForfaits->etat == 1)
                            @include('admin.souscrire-forfaits.form-disable-souscrire-forfaits')
                        @elseif($tabSouscrireForfaits->etat == 0)
                            @include('admin.souscrire-forfaits.form-active-souscrire-forfaits')
                        @endif
                    </div>
                </td>
            </tr>
        @endforeach
    </tbody>
</table>
