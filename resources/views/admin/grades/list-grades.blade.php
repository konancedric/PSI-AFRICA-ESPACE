<table id="data_table" class="table">
    <thead>
        <tr>
            <th>{{ __('Date Add')}}</th>
            <th>{{ __('Grade')}}</th>
            <th>{{ __('Update Add')}}</th>
            <th>{{ __('Action')}}</th>
        </tr>
    </thead>
    <tbody>
        @foreach ($dataGrades as $tabGrades)
            <tr>
                <td>{{ $tabGrades->created_at }}</td>
                <td>
                    @if($tabGrades->etat == 1)
                        <span class="badge badge-success badge-pill"> {{ $tabGrades->libelle }}</span>
                    @elseif($tabGrades->etat == 0)
                        <span class="badge badge-danger badge-pill"> {{ $tabGrades->libelle }}</span>
                    @endif
                </td>
                <td>{{ $tabGrades->updated_at }}</td>
                <td>
                    <div class="table-actions">
                        @include('admin.grades.form-update-grades')
                        @if($tabGrades->etat == 1)
                            @include('admin.grades.form-disable-grades')
                        @elseif($tabGrades->etat == 0)
                            @include('admin.grades.form-active-grades')
                        @endif
                    </div>
                </td>
            </tr>
        @endforeach
    </tbody>
</table>
