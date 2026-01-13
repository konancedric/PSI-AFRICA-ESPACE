<table id="data_table" class="table">
    <thead>
        <tr>
            <th>{{ __('Date Add')}}</th>
            <th>{{ __('Catégorie')}}</th>
            <th>{{ __('Actualite')}}</th>
            <th>{{ __('Resumé')}}</th>
            <th>{{ __('Image')}}</th>
            <th>{{ __('Update Add')}}</th>
            <th width="30%">{{ __('Action')}}</th>
        </tr>
    </thead>
    <tbody>
        @foreach ($dataActualites as $tabActualites)
            <tr>
                <td>{{ $tabActualites->created_at }}</td>
                <td>
                    @foreach(App\Models\Categories::where('id', $tabActualites->id_categorie)->get() as $CategoriesSearch)
                        {{ $CategoriesSearch->libelle }}
                    @endforeach
                </td>
                <td>
                    @if($tabActualites->etat == 1)
                        <span class="badge badge-success badge-pill"> {{ $tabActualites->titre }}</span>
                    @elseif($tabActualites->etat == 0)
                        <span class="badge badge-danger badge-pill"> {{ $tabActualites->titre }}</span>
                    @endif
                </td>
                <td>{!! $tabActualites->resume !!}</td>
                <td>
                    <img src="{{$linkEditor}}/{{ $tabActualites->img_actualite }}" width="20%" />
                </td>
                <td>{{ $tabActualites->updated_at }}</td>
                <td width="30%">
                    <div class="table-actions">
                        @include('admin.actualites.view-actualites')
                        @include('admin.actualites.form-update-actualites')
                        @if($tabActualites->etat == 1)
                            @include('admin.actualites.form-disable-actualites')
                        @elseif($tabActualites->etat == 0)
                            @include('admin.actualites.form-active-actualites')
                        @endif
                    </div>
                </td>
            </tr>
        @endforeach
    </tbody>
</table>
