<table id="data_table" class="table">
    <thead>
        <tr>
            <th>{{ __('Date Add')}}</th>
            <th>{{ __('Faq')}}</th>
            <th>{{ __('Resumé')}}</th>
            <th>{{ __('Image')}}</th>
            <th>{{ __('Update Add')}}</th>
            <th width="30%">{{ __('Action')}}</th>
        </tr>
    </thead>
    <tbody>
        @foreach ($dataFaqs as $tabFaqs)
            <tr>
                <td>{{ $tabFaqs->created_at }}</td>
                <td>
                    @if($tabFaqs->etat == 1)
                        <span class="badge badge-success badge-pill"> {{ $tabFaqs->titre }}</span>
                    @elseif($tabFaqs->etat == 0)
                        <span class="badge badge-danger badge-pill"> {{ $tabFaqs->titre }}</span>
                    @endif
                </td>
                <td>{!! $tabFaqs->resume !!}</td>
                <td>
                    <img src="{{$linkEditor}}/{{ $tabFaqs->img_faq }}" width="20%" />
                </td>
                <td>{{ $tabFaqs->updated_at }}</td>
                <td width="30%">
                    <div class="table-actions">
                        @include('admin.faqs.view-faqs')
                        @include('admin.faqs.form-update-faqs')
                        @if($tabFaqs->etat == 1)
                            @include('admin.faqs.form-disable-faqs')
                        @elseif($tabFaqs->etat == 0)
                            @include('admin.faqs.form-active-faqs')
                        @endif
                    </div>
                </td>
            </tr>
        @endforeach
    </tbody>
</table>
