<table id="data_table" class="table">
    <thead>
        <tr>
            <th>{{ __('Date Add')}}</th>
            <th>{{ __('Election')}}</th>
            <th>{{ __('Type E.')}}</th>
            <th>{{ __('Grade')}}</th>
            <th>{{ __('Catégorie')}}</th>
            <th>{{ __('Tête de Liste')}}</th>
            <th>{{ __('Date')}}</th>
            <th>{{ __('Update Add')}}</th>
            <th>{{ __('Action')}}</th>
        </tr>
    </thead>
    <tbody>
        @foreach ($dataElections as $tabElections)
            <?php  
                if($tabElections->type_election == 1)
                {
                    $dataElecteur = App\Models\User::where('id_grade', $tabElections->id_grade)->where('id_categorie', $tabElections->id_categorie)->get();
                }
                elseif($tabElections->type_election == 2)
                {  
                    $dataElecteur = App\Models\User::where('id_categorie', $tabElections->id_categorie)->get();
                }
                elseif($tabElections->type_election == 3)
                {
                    $dataElecteur = App\Models\User::where('id_grade', $tabElections->id_grade)->get();
                }
                else
                {
                    $dataElecteur = App\Models\User::where('id_grade', $tabElections->id_grade)->where('id_categorie', $tabElections->id_categorie)->get();
                }

                if($tabElections->etat == 1)
                {
                    $color = "success";
                }
                elseif($tabElections->etat == 0)
                {
                    $color = "danger";
                }  
                elseif($tabElections->etat == 3)
                {
                    $color = "primary";
                }
                else
                {
                    $color = "dark";
                }
                $dataVotant = App\Models\Votes::where('id_election', $tabElections->id)->get();
                $nbrElecteur = count($dataElecteur); 
                $nbrVotant = count($dataVotant);
                $pResultat = (($nbrVotant * 100) / 100);
            ?>
            <tr>
                <td>{{ $tabElections->created_at }}</td>
                <td>
                   <span class="badge badge-{{$color}} badge-pill"> {{ $tabElections->libelle }}</span>
                </td>
                <td>
                    @if($tabElections->type_election == 1)
                        <span class="badge badge-warning badge-pill"> G&C </span>
                    @elseif($tabElections->type_election == 3)
                        <span class="badge badge-info badge-pill"> G </span>
                    @elseif($tabElections->type_election == 2)
                        <span class="badge badge-dark badge-pill"> C </span>
                    @endif
                </td>
                <td>
                    @foreach(App\Models\Grades::where('id', $tabElections->id_grade)->get() as $GradeSearch)
                        {{ $GradeSearch->libelle }}
                    @endforeach
                </td>
                <td>
                    @foreach(App\Models\Categories::where('id', $tabElections->id_categorie)->get() as $CategorieSearch)
                        {{ $CategorieSearch->libelle }}
                    @endforeach
                </td>
                <td>
                    <?php
                        $tabElecteur = explode('~', $tabElections->tete_liste);
                        for($i = 0; $i < count($tabElecteur); $i++)
                        {
                            foreach(App\Models\User::where('id', $tabElecteur[$i])->get() as $UserSearch)
                            {
                    ?>
                         <span class="badge badge-dark badge-pill" > {{$UserSearch->name}} </span>
                    <?php
                            }
                        }
                    ?>
                </td>
                <td>
                    @if($tabElections->date_debut != "" AND $tabElections->date_fin != "")
                        du {{ $tabElections->date_debut }} au {{ $tabElections->date_fin }}
                    @elseif($tabElections->date_debut != "")
                        {{ $tabElections->date_debut }}
                    @else
                        Pas de date defini
                    @endif
                </td>
                <td>{{ $tabElections->updated_at }}</td>
                <td>
                    <div class="table-actions">
                        @include('admin.elections.gestion-election')
                        @include('admin.elections.form-update-elections')
                        @if($tabElections->etat == 1)
                            @include('admin.elections.form-disable-elections')
                        @elseif($tabElections->etat == 0)
                            @include('admin.elections.form-active-elections')
                        @endif
                    </div>
                </td>
            </tr>
        @endforeach
    </tbody>
</table>