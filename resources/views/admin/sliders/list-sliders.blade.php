<table id="data_table" class="table">
    <thead>
        <tr>
            <th>{{ __('Date Add')}}</th>
            <th>{{ __('Image')}}</th>
            <th>{{ __('Update Add')}}</th>
            <th>{{ __('Action')}}</th>
        </tr>
    </thead>
    <tbody>
        @foreach ($dataSliders as $tabSliders)
            <tr>
                <td>
                    @if($tabSliders->etat == 1)
                        <span class="badge badge-success badge-pill"> {{ $tabSliders->created_at }}</span>
                    @elseif($tabSliders->etat == 0)
                        <span class="badge badge-danger badge-pill"> {{ $tabSliders->created_at }}</span>
                    @endif
                </td>
                <td><img src="/upload/sliders/{{ $tabSliders->img_sliders }}" class="w-50" /></td>
                <td>{{ $tabSliders->updated_at }}</td>
                <td>
                    <div class="table-actions">
                       <?php /* @include('admin.sliders.form-update-sliders') */ ?>
                        @if($tabSliders->etat == 1)
                            @include('admin.sliders.form-disable-sliders')
                        @elseif($tabSliders->etat == 0)
                            @include('admin.sliders.form-active-sliders')
                        @endif
                    </div>
                </td>
            </tr>
        @endforeach
    </tbody>
</table>
