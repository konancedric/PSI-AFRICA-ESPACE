<table id="data_table" class="table">
    <thead>
        <tr>
            <th>{{ __('Date Add')}}</th>
            <th>{{ __('Partenaire')}}</th>
            <th>{{ __('Site web')}}</th>
            <th>{{ __('Logo')}}</th>
            <th>{{ __('Update Add')}}</th>
            <th>{{ __('Action')}}</th>
        </tr>
    </thead>
    <tbody>
        @foreach ($dataPartenaires as $tabPartenaires)
            <tr>
                <td>{{ $tabPartenaires->created_at }}</td>
                <td>
                    @if($tabPartenaires->etat == 1)
                        <span class="badge badge-success badge-pill"> {{ $tabPartenaires->libelle }}</span>
                    @elseif($tabPartenaires->etat == 0)
                        <span class="badge badge-danger badge-pill"> {{ $tabPartenaires->libelle }}</span>
                    @endif
                </td>
                <td><a href="{{ $tabPartenaires->site_web }}" target="_blank">{{ $tabPartenaires->site_web }} </a> </td>
                <td><img src="/upload/partenaires/{{ $tabPartenaires->img_partenaires }}" class="w-100" /></td>
                <td>{{ $tabPartenaires->updated_at }}</td>
                <td>
                    <div class="table-actions">
                        @include('admin.partenaires.form-update-partenaires')
                        @if($tabPartenaires->etat == 1)
                            @include('admin.partenaires.form-disable-partenaires')
                        @elseif($tabPartenaires->etat == 0)
                            @include('admin.partenaires.form-active-partenaires')
                        @endif
                    </div>
                </td>
            </tr>
        @endforeach
    </tbody>
</table>
