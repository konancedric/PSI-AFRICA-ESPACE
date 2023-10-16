<table id="data_table" class="table">
    <thead>
        <tr>
            <th>{{ __('Date Add')}}</th>
            <th>{{ __('Nom & Prénom')}}</th>
            <th>{{ __('Matricule')}}</th>
            <th>{{ __('Grade')}}</th>
            <th>{{ __('Catégorie')}}</th>
            <th>{{ __('Contact')}}</th>
            <th>{{ __('Photo')}}</th>
            <th>{{ __('Update Add')}}</th>
            <th>{{ __('Action')}}</th>
        </tr>
    </thead>
    <tbody>
        @foreach ($dataUsers as $tabUsers)
            <tr>
                <td>{{ $tabUsers->created_at }}</td>
                <td>
                    @if($tabUsers->etat == 1)
                        <span class="badge badge-success badge-pill"> {{ $tabUsers->name }}</span>
                    @elseif($tabUsers->etat == 0)
                        <span class="badge badge-danger badge-pill"> {{ $tabUsers->name }}</span>
                    @endif
                </td>
                <td>{{ $tabUsers->matricule }}</td>
                <td>
                    @foreach(App\Models\Grades::where('id', $tabUsers->id_grade)->get() as $GradeSearch)
                        {{ $GradeSearch->libelle }}
                    @endforeach
                </td>
                <td>
                    @foreach(App\Models\Categories::where('id', $tabUsers->id_categorie)->get() as $CategorieSearch)
                        {{ $CategorieSearch->libelle }}
                    @endforeach
                </td>
                <td>{{ $tabUsers->contact }}</td>
                <td>
                    <img src="upload/users/{{$tabUsers->photo_user }}"  width="10%"/>
                </td>
                <td>{{ $tabUsers->updated_at }}</td>
                <td>
                    <div class="table-actions">
                        @include('admin.users.form-update-users')
                        @if($tabUsers->etat == 1)
                            @include('admin.users.form-disable-users')
                        @elseif($tabUsers->etat == 0)
                            @include('admin.users.form-active-users')
                        @endif
                    </div>
                </td>
            </tr>
        @endforeach
    </tbody>
</table>
