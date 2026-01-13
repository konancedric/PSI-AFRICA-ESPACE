<table id="data_table" class="table">
    <thead>
        <tr>
            <th>{{ __('Date Add')}}</th>
            <th>{{ __('Libelle')}}</th>
            <th>{{ __('Url Youtube')}}</th>
            <th>{{ __('Update Add')}}</th>
            <th>{{ __('Action')}}</th>
        </tr>
    </thead>
    <tbody>
        @foreach ($dataGalerieVideo as $tabGalerieVideo)
            <tr>
                <td>{{ $tabGalerieVideo->created_at }}</td>
                <td>
                    @if($tabGalerieVideo->etat == 1)
                        <span class="badge badge-success badge-pill"> {{ $tabGalerieVideo->libelle }}</span>
                    @elseif($tabGalerieVideo->etat == 0)
                        <span class="badge badge-danger badge-pill"> {{ $tabGalerieVideo->libelle }}</span>
                    @endif
                </td>
                <td>{{ $tabGalerieVideo->save_url }}</td>
                <td>{{ $tabGalerieVideo->updated_at }}</td>
                <td>
                    <div class="table-actions">
                        @include('admin.galerie-video.form-update-galerie-video')
                        @if($tabGalerieVideo->etat == 1)
                            @include('admin.galerie-video.form-disable-galerie-video')
                        @elseif($tabGalerieVideo->etat == 0)
                            @include('admin.galerie-video.form-active-galerie-video')
                        @endif
                    </div>
                </td>
            </tr>
        @endforeach
    </tbody>
</table>
