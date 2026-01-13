 <button class="btn btn-success btn-sm" href="#MAddImageToCategoriesImagese{{ $tabCategoriesImages->id }}" data-toggle="modal" data-target="#MAddImageToCategoriesImagese{{ $tabCategoriesImages->id }}" title="Gestions une image"><i class="fa fa-eye"></i> Gestion des images</button>
<div class="modal fade edit-layout-modal" id="MAddImageToCategoriesImagese{{ $tabCategoriesImages->id }}" tabindex="-1" role="dialog" aria-labelledby="MAddImageToCategoriesImagese{{ $tabCategoriesImages->id }}Label" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header bg-success">
                <h5 class="modal-title text-white" id="MAddImageToCategoriesImagese{{ $tabCategoriesImages->id }}Label">
                    <b>Gestions des images à " {{ $tabCategoriesImages->libelle }} "</b></h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times; </span></button>
            </div>
            <div class="modal-body text-justify">
                <div class="card">
                    <div class="card-header bg-dark text-white text-center">
                        GESTION DES IMAGES
                    </div>
                    <div class="card-body">
                        <form class="forms-sample" method="POST" action="{{url('galerie-images/create')}}" enctype="multipart/form-data">
                            @csrf
                            <div class="row">
                                <input type="hidden" name="user1d" value="{{$user1d}}">
                                <input type="hidden" name="id_categorie" value="{{ $tabCategoriesImages->id }}">
                                <div class="col-sm-6">
                                    <div class="form-group">
                                        <label for="libelle"><i class="fas fa-tag"></i> {{ __('Libelle')}}<span class="text-red">*</span></label>
                                        <input type="text" class="form-control" id="libelle" name="libelle" placeholder="Libelle" required>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label for="save_url"><i class="fa fa-file-image-o"></i> Image (jpeg, png, pdf, (taille max 2 Mo))  *</label>
                                        <input type="file" class="form-control" id="save_url" placeholder="Image (jpeg, png, pdf, (taille max 10Mo))" name="save_url" accept=".jpeg, .png, .jpg" required>
                                    </div>
                                </div>
                                <div class="col-sm-6">
                                    <div class="form-group"><br/>
                                        <button type="submit" class="btn btn-warning btn-rounded"><i class="ik ik-check-circle"></i> {{ __('Enregistrer')}}</button>
                                    </div>
                                </div>
                            </div>
                        </form>
                    </div>
                    <div class="card-footer">
                        <table id="data_table" class="table">
                            <thead>
                                <tr>
                                    <th>{{ __('Date Add')}}</th>
                                    <th>{{ __('Libelle')}}</th>
                                    <th>{{ __('Image')}}</th>
                                    <th>{{ __('Update Add')}}</th>
                                    <th>{{ __('Action')}}</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach (App\Models\GalerieImages::where('id_categorie', $tabCategoriesImages->id)->orderBy('libelle', 'asc')->get() as $tabGalerieImages)
                                    <tr>
                                        <td>{{ $tabGalerieImages->created_at }}</td>
                                        <td>
                                            @if($tabGalerieImages->etat == 1)
                                                <span class="badge badge-success badge-pill"> {{ $tabGalerieImages->libelle }}</span>
                                            @elseif($tabGalerieImages->etat == 0)
                                                <span class="badge badge-danger badge-pill"> {{ $tabGalerieImages->libelle }}</span>
                                            @endif
                                        </td>
                                        <td><img src="/upload/galerie-images/{{ $tabGalerieImages->save_url }}" class="w-100" /></td>
                                        <td>{{ $tabGalerieImages->updated_at }}</td>
                                        <td>
                                            <div class="table-actions">
                                                <?php /*
                                                @if($tabGalerieImages->etat == 1)
                                                    @include('admin.partenaires.form-disable-partenaires')
                                                @elseif($tabGalerieImages->etat == 0)
                                                    @include('admin.partenaires.form-active-partenaires')
                                                @endif
                                                */ ?>
                                            </div>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>

                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-dismiss="modal">{{ __('Fermer')}}</button>
            </div>
        </div>
    </div>
</div>