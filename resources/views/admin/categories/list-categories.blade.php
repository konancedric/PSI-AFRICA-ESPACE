<table id="data_table" class="table">
    <thead>
        <tr>
            <th>{{ __('Date Add')}}</th>
            <th>{{ __('Categorie')}}</th>
            <th>{{ __('Update Add')}}</th>
            <th>{{ __('Action')}}</th>
        </tr>
    </thead>
    <tbody>
        @foreach ($dataCategories as $tabCategories)
            <tr>
                <td>{{ $tabCategories->created_at }}</td>
                <td>
                    @if($tabCategories->etat == 1)
                        <span class="badge badge-success badge-pill"> {{ $tabCategories->libelle }}</span>
                    @elseif($tabCategories->etat == 0)
                        <span class="badge badge-danger badge-pill"> {{ $tabCategories->libelle }}</span>
                    @endif
                </td>
                <td>{{ $tabCategories->updated_at }}</td>
                <td>
                    <div class="table-actions">
                        @include('admin.categories.form-update-categories')
                        @if($tabCategories->etat == 1)
                            @include('admin.categories.form-disable-categories')
                        @elseif($tabCategories->etat == 0)
                            @include('admin.categories.form-active-categories')
                        @endif
                    </div>
                </td>
            </tr>
        @endforeach
    </tbody>
</table>
