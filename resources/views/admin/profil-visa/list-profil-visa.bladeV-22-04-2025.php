<table id="data_table" class="table">
    <thead>
        <tr>
            <th>{{ __('Date Add')}}</th>
            <th>{{ __('N° D.')}}</th>
            <th>{{ __('Type Profile')}}</th>
            <th>{{ __('Etape')}}</th>
            <th>{{ __('Statuts')}}</th>
            <th>{{ __('Update Add')}}</th>
            <th width="30%">{{ __('Action')}}</th>
        </tr>
    </thead>
    <tbody>
        @foreach ($dataProfilVisa as $tabProfilVisa)
            <tr>
                <td>{{ $tabProfilVisa->created_at }}</td>
                <td>
                    @if($tabProfilVisa->etat == 1)
                        <span class="badge badge-success badge-pill"> {{ $tabProfilVisa->numero_profil_visa }}</span>
                    @elseif($tabProfilVisa->etat == 0)
                        <span class="badge badge-danger badge-pill"> {{ $tabProfilVisa->numero_profil_visa }}</span>
                    @else
                        <span class="badge badge-danger badge-pill"> {{ $tabProfilVisa->numero_profil_visa }}</span>
                    @endif
                </td>
                <td>
                    @if($tabProfilVisa->type_profil_visa == "tourisme")
                        <span class="badge badge-primary badge-pill text-white"> VISA Tourisme</span>
                        @php($bgColor="primary")
                    @elseif($tabProfilVisa->type_profil_visa == "mineur")
                        <span class="badge badge-warning badge-pill text-white"> VISA Mineur</span>
                        @php($bgColor="warning")
                    @elseif($tabProfilVisa->type_profil_visa == "etude")
                        <span class="badge badge-info badge-pill text-white"> VISA Etude</span>
                        @php($bgColor="info")
                    @elseif($tabProfilVisa->type_profil_visa == "travail")
                        <span class="badge badge-success badge-pill text-white"> VISA Travail</span>
                        @php($bgColor="success")
                    @else
                        <span class="badge badge-dark badge-pill text-white">Autre</span>
                        @php($bgColor="dark")
                    @endif
                </td>
                <td>{{ $tabProfilVisa->etape }}</td>
                <td>
                    @foreach(App\Models\StatutsEtat::where('id', $tabProfilVisa->id_statuts_etat)->get() as $tabStatutsEtat)
                        <span class="badge badge-pill" style="background-color:<?=$tabStatutsEtat->bg_color?>">{{ $tabStatutsEtat->libelle }}</span>
                    @endforeach
                </td>
                <td>{{ $tabProfilVisa->updated_at }}</td>
                <td width="30%">
                    <div class="table-actions">
                        @if($tabProfilVisa->type_profil_visa == "mineur")
                            @include('admin.profil-visa.view-profil-visa-mineur')
                        @else
                            @include('admin.profil-visa.view-profil-visa')
                        @endif
                        @include('admin.profil-visa.view-documents')
                        @can('manage_profil_visa')
                            @include('admin.profil-visa.form-add-statuts-etat')
                            @if($tabProfilVisa->etat == 1)
                                @include('admin.profil-visa.form-disable-profil-visa')
                            @elseif($tabProfilVisa->etat == 0)
                                @include('admin.profil-visa.form-active-profil-visa')
                            @else
                                @include('admin.profil-visa.form-active-profil-visa')
                            @endif
                            @include('admin.profil-visa.form-add-message-profil-visa')
                            @include('admin.profil-visa.form-delete-profil-visa')
                        @endcan
                        @include('admin.profil-visa.check-update-profil-visa')
                        @include('admin.profil-visa.view-message-profil-visa')

                    </div>
                </td>
            </tr>
        @endforeach
    </tbody>
</table>
{{ $dataProfilVisa->links() }}
