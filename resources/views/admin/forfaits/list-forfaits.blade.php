<table id="data_table" class="table">
    <thead>
        <tr>
            <th>{{ __('Date Add')}}</th>
            <th>{{ __('Actualite')}}</th>
            <th>{{ __('Prix')}}</th>
            <th>{{ __('Resumé')}}</th>
            <th>{{ __('Image')}}</th>
            <th>{{ __('Update Add')}}</th>
            <th width="30%">{{ __('Action')}}</th>
        </tr>
    </thead>
    <tbody>
        @foreach ($dataForfaits as $tabForfaits)
            <tr>
                <td>{{ $tabForfaits->created_at }}</td>
                <td>
                    @if($tabForfaits->etat == 1)
                        <span class="badge badge-success badge-pill"> {{ $tabForfaits->titre }}</span>
                    @elseif($tabForfaits->etat == 0)
                        <span class="badge badge-danger badge-pill"> {{ $tabForfaits->titre }}</span>
                    @endif
                </td>
                <td>{{$tabForfaits->prix}}</td>
                <td>{!! $tabForfaits->resume !!}</td>
                <td>
                    <img src="{{$linkEditor}}/{{ $tabForfaits->img_forfait }}" width="20%" />
                </td>
                <td>{{ $tabForfaits->updated_at }}</td>
                <td width="30%">
                    <div class="table-actions">
                        @include('admin.forfaits.view-forfaits')
                        @include('admin.forfaits.form-update-forfaits')
                        @if($tabForfaits->etat == 1)
                            @include('admin.forfaits.form-disable-forfaits')
                        @elseif($tabForfaits->etat == 0)
                            @include('admin.forfaits.form-active-forfaits')
                        @endif
                    </div>
                </td>
            </tr>
        @endforeach
    </tbody>
</table>
