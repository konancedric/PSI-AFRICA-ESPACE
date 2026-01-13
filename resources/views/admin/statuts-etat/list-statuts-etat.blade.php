<table id="data_table" class="table">
    <thead>
        <tr>
            <th>{{ __('Date Add')}}</th>
            <th>{{ __('Libelle')}}</th>
            <th>{{ __('Couleur')}}</th>
            <th>{{ __('Update Add')}}</th>
            <th>{{ __('Action')}}</th>
        </tr>
    </thead>
    <tbody>
        @foreach ($dataStatutsEtat as $tabStatutsEtat)
            <tr>
                <td>{{ $tabStatutsEtat->created_at }}</td>
                <td>
                    @if($tabStatutsEtat->etat == 1)
                        <span class="badge badge-success badge-pill"> {{ $tabStatutsEtat->libelle }}</span>
                    @elseif($tabStatutsEtat->etat == 0)
                        <span class="badge badge-danger badge-pill"> {{ $tabStatutsEtat->libelle }}</span>
                    @endif
                </td>
                <td><span class="badge badge-pill" style="background-color:<?=$tabStatutsEtat->bg_color?>">{{ $tabStatutsEtat->bg_color }}</span></td>
                <td>{{ $tabStatutsEtat->created_at }}</td>
                <td>
                    <div class="table-actions">
                        @include('admin.statuts-etat.form-update-statuts-etat')
                        @if($tabStatutsEtat->etat == 1)
                            @include('admin.statuts-etat.form-disable-statuts-etat')
                        @elseif($tabStatutsEtat->etat == 0)
                            @include('admin.statuts-etat.form-active-statuts-etat')
                        @endif
                    </div>
                </td>
            </tr>
        @endforeach
    </tbody>
</table>
