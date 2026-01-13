<table id="data_table" class="table">
    <thead>
        <tr>
            <th>{{ __('Date Add')}}</th>
            <th>{{ __('Ville')}}</th>
            <th>{{ __('Update Add')}}</th>
            <th>{{ __('Action')}}</th>
        </tr>
    </thead>
    <tbody>
        @foreach ($dataVilles as $tabVilles)
            <tr>
                <td>{{ $tabVilles->created_at }}</td>
                <td>
                    @if($tabVilles->etat == 1)
                        <span class="badge badge-success badge-pill"> {{ $tabVilles->libelle }}</span>
                    @elseif($tabVilles->etat == 0)
                        <span class="badge badge-danger badge-pill"> {{ $tabVilles->libelle }}</span>
                    @endif
                </td>
                <td>{{ $tabVilles->updated_at }}</td>
                <td>
                    <div class="table-actions">
                        @include('admin.villes.view-villes')
                        <a class="btn btn-success btn-sm text-white" href="/villes/show/{{ $tabVilles->id }}"title="Historique de la ville de {{ $tabVilles->libelle }}">Editer l'historique <i class="fa fa-edit"></i></a>
                        @if($tabVilles->etat == 1)
                            @include('admin.villes.form-disable-villes')
                        @elseif($tabVilles->etat == 0)
                            @include('admin.villes.form-active-villes')
                        @endif
                    </div>
                </td>
            </tr>
        @endforeach
    </tbody>
</table>
