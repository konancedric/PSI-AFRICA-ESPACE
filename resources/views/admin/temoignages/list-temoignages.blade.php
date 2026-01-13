<table id="data_table" class="table">
    <thead>
        <tr>
            <th>{{ __('Date Add')}}</th>
            <th>{{ __('Temoignage')}}</th>
            <th>{{ __('Resumé')}}</th>
            <th>{{ __('Image')}}</th>
            <th>{{ __('Update Add')}}</th>
            <th width="30%">{{ __('Action')}}</th>
        </tr>
    </thead>
    <tbody>
        @foreach ($dataTemoignages as $tabTemoignages)
            <tr>
                <td>{{ $tabTemoignages->created_at }}</td>
                <td>
                    @if($tabTemoignages->etat == 1)
                        <span class="badge badge-success badge-pill"> {{ $tabTemoignages->titre }}</span>
                    @elseif($tabTemoignages->etat == 0)
                        <span class="badge badge-danger badge-pill"> {{ $tabTemoignages->titre }}</span>
                    @endif
                </td>
                <td>{!! $tabTemoignages->resume !!}</td>
                <td>
                    <img src="{{$linkEditor}}/{{ $tabTemoignages->img_temoignage }}" width="20%" />
                </td>
                <td>{{ $tabTemoignages->updated_at }}</td>
                <td width="30%">
                    <div class="table-actions">
                        @include('admin.temoignages.view-temoignages')
                        @include('admin.temoignages.form-update-temoignages')
                        @if($tabTemoignages->etat == 1)
                            @include('admin.temoignages.form-disable-temoignages')
                        @elseif($tabTemoignages->etat == 0)
                            @include('admin.temoignages.form-active-temoignages')
                        @endif
                    </div>
                </td>
            </tr>
        @endforeach
    </tbody>
</table>
