<table id="data_table" class="table">
    <thead>
        <tr>
            <th>{{ __('Date Add')}}</th>
            <th>{{ __('Categorie')}}</th>
            <th>{{ __('Sous Categorie')}}</th>
            <th>{{ __('Update Add')}}</th>
            <th>{{ __('Action')}}</th>
        </tr>
    </thead>
    <tbody>
        @foreach ($dataSousCategories as $tabSousCategories)
            <tr>
                <td>{{ $tabSousCategories->created_at }}</td>
                <td>
                    @foreach(App\Models\Categories::where('id', $tabSousCategories->id_categorie)->get() as $CategoriesSearch)
                        {{ $CategoriesSearch->libelle }}
                    @endforeach
                </td>
                <td>
                    @if($tabSousCategories->etat == 1)
                        <span class="badge badge-success badge-pill"> {{ $tabSousCategories->libelle }}</span>
                    @elseif($tabSousCategories->etat == 0)
                        <span class="badge badge-danger badge-pill"> {{ $tabSousCategories->libelle }}</span>
                    @endif
                </td>
                <td>{{ $tabSousCategories->updated_at }}</td>
                <td>
                    <div class="table-actions">
                        @include('admin.souscategories.form-update-souscategories')
                        @if($tabSousCategories->etat == 1)
                            @include('admin.souscategories.form-disable-souscategories')
                        @elseif($tabSousCategories->etat == 0)
                            @include('admin.souscategories.form-active-souscategories')
                        @endif
                    </div>
                </td>
            </tr>
        @endforeach
    </tbody>
</table>
