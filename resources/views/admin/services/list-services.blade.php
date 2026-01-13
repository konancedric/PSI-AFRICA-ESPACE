<table id="data_table" class="table">
    <thead>
        <tr>
            <th>{{ __('Date Add')}}</th>
            <th>{{ __('Service')}}</th>
            <th>{{ __('Resumé')}}</th>
            <th>{{ __('Texte Bouton')}}</th>
            <th>{{ __('Image')}}</th>
            <th>{{ __('Update Add')}}</th>
            <th width="30%">{{ __('Action')}}</th>
        </tr>
    </thead>
    <tbody>
        @foreach ($dataServices as $tabServices)
            <tr>
                <td>{{ $tabServices->created_at }}</td>
                <td>
                    @if($tabServices->etat == 1)
                        <span class="badge badge-success badge-pill"> {{ $tabServices->titre }}</span>
                    @elseif($tabServices->etat == 0)
                        <span class="badge badge-danger badge-pill"> {{ $tabServices->titre }}</span>
                    @endif
                </td>
                <td>{!! $tabServices->resume !!}</td>
                <td>{{ $tabServices->texte_bouton }}</td>
                <td>
                    <img src="{{$linkEditor}}/{{ $tabServices->img_service }}" width="20%" />
                </td>
                <td>{{ $tabServices->updated_at }}</td>
                <td width="30%">
                    <div class="table-actions">
                        @include('admin.services.view-services')
                        @include('admin.services.form-update-services')
                        @if($tabServices->etat == 1)
                            @include('admin.services.form-disable-services')
                        @elseif($tabServices->etat == 0)
                            @include('admin.services.form-active-services')
                        @endif
                    </div>
                </td>
            </tr>
        @endforeach
    </tbody>
</table>
