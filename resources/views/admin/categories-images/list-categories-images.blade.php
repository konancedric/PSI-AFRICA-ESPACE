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
        @foreach ($dataCategoriesImages as $tabCategoriesImages)
            <tr>
                <td>{{ $tabCategoriesImages->created_at }}</td>
                <td>
                    @if($tabCategoriesImages->etat == 1)
                        <span class="badge badge-success badge-pill"> {{ $tabCategoriesImages->libelle }}</span>
                    @elseif($tabCategoriesImages->etat == 0)
                        <span class="badge badge-danger badge-pill"> {{ $tabCategoriesImages->libelle }}</span>
                    @endif
                </td>
                <td>{{ $tabCategoriesImages->updated_at }}</td>
                <td>
                    <div class="table-actions">
                        @include('admin.categories-images.galerie-images')
                        @include('admin.categories-images.form-update-categories-images')
                        @if($tabCategoriesImages->etat == 1)
                            @include('admin.categories-images.form-disable-categories-images')
                        @elseif($tabCategoriesImages->etat == 0)
                            @include('admin.categories-images.form-active-categories-images')
                        @endif
                    </div>
                </td>
            </tr>
        @endforeach
    </tbody>
</table>
