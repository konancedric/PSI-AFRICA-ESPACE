<table id="data_table" class="table">
    <thead>
        <tr>
            <th>{{ __('Date Add')}}</th>
            <th>{{ __('Entreprise')}}</th>
            <th>{{ __('Ville')}}</th>
            <th>{{ __('Catégorie')}}</th>
            <th>{{ __('Contact')}}</th>
            <th>{{ __('Services')}}</th>
            <th>{{ __('Action')}}</th>
        </tr>
    </thead>
    <tbody>
        @foreach ($dataEntreprises as $tabEntreprises)
            <tr>
                <td>{{ $tabEntreprises->created_at }}</td>
                <td>
                    @if($tabEntreprises->etat == 1)
                        <span class="badge badge-success badge-pill"> {{ $tabEntreprises->denomination }}</span>
                    @elseif($tabEntreprises->etat == 0)
                        <span class="badge badge-danger badge-pill"> {{ $tabEntreprises->denomination }}</span>
                    @endif
                </td>
                <td>
                    @foreach(App\Models\Villes::where('id', $tabEntreprises->id_ville)->get() as $VillesSearch)
                        {{ $VillesSearch->libelle }}
                    @endforeach
                </td>
                <td>
                    @foreach(App\Models\SousCategories::where('id', $tabEntreprises->id_souscategorie)->get() as $SousCategoriesSearch)
                        @foreach(App\Models\Categories::where('id', $SousCategoriesSearch->id_categorie)->get() as $CategoriesSearch)
                            {{ $CategoriesSearch->libelle }} - {{ $SousCategoriesSearch->libelle }}
                        @endforeach
                    @endforeach
                </td>
                <td>{{ $tabEntreprises->email }} - {{ $tabEntreprises->contact }}</td>
                <td>
                   {{count(App\Models\Services::where('ent1d', $tabEntreprises->id)->get() )}} service(s)
                </td>
                <td>
                    <div class="table-actions">
                        <?php 
                        /*
                            @if($tabEntreprises->etat == 1)
                                @include('admin.categories.form-disable-categories')
                            @elseif($tabEntreprises->etat == 0)
                                @include('admin.categories.form-active-categories')
                            @endif
                        */ ?>
                    </div>
                </td>
            </tr>
        @endforeach
    </tbody>
</table>
