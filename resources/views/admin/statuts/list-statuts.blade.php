<table id="data_table" class="table">
    <thead>
        <tr>
            <th>{{ __('Date Add')}}</th>
            <th>{{ __('Libelle')}}</th>
            <th>{{ __('Couleur')}}</th>
            <th>{{ __('Numero Etape')}}</th>
            <th>{{ __('Description')}}</th>
            <th>{{ __('Update Add')}}</th>
            <th>{{ __('Action')}}</th>
        </tr>
    </thead>
    <tbody>
        @foreach ($dataStatuts as $tabStatuts)
            <tr>
                <td>{{ $tabStatuts->created_at }}</td>
                <td>
                    @if($tabStatuts->etat == 1)
                        <span class="badge badge-success badge-pill"> {{ $tabStatuts->libelle }}</span>
                    @elseif($tabStatuts->etat == 0)
                        <span class="badge badge-danger badge-pill"> {{ $tabStatuts->libelle }}</span>
                    @endif
                </td>
                <td><span class="badge badge-pill" style="background-color:<?=$tabStatuts->bg_color?>">{{ $tabStatuts->bg_color }}</span></td>
                <td>{{ $tabStatuts->numero_etape }}</td>
                <td>{{ $tabStatuts->description }}</td>
                <td>{{ $tabStatuts->created_at }}</td>
                <td>
                    <div class="table-actions">
                        @include('admin.statuts.form-update-statuts')
                        @if($tabStatuts->etat == 1)
                            @include('admin.statuts.form-disable-statuts')
                        @elseif($tabStatuts->etat == 0)
                            @include('admin.statuts.form-active-statuts')
                        @endif
                    </div>
                </td>
            </tr>
        @endforeach
    </tbody>
</table>
