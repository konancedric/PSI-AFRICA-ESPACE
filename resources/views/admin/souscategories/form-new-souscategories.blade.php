<form class="forms-sample" method="POST" action="{{url('souscategories/create')}}">
    @csrf
    <div class="row">
        <input type="hidden" name="user1d" value="{{$user1d}}">
        <div class="col-sm-4">
            <div class="form-group">
                <label for="id_categorie"><i class="fa fa-tags"></i> {{ __('Categories')}}<span class="text-red">*</span></label>
                <select class="form-control select2" id="id_categorie" name="id_categorie" required>
                    <option value="">Selectionnez la categorie</option>
                     @foreach ($dataCategories as $tabCategories)
                        <option value="{{ $tabCategories->id }}">{{ $tabCategories->libelle }}</option>
                     @endforeach
                </select>
            </div>
        </div>
        <div class="col-sm-4">
            <div class="form-group">
                <label for="libelle"><i class="fas fa-tag"></i> {{ __('Sous Categorie')}}<span class="text-red">*</span></label>
                <input type="text" class="form-control" id="libelle" name="libelle" placeholder="Libelle" required>
            </div>
        </div>
        <div class="col-sm-4">
            <div class="form-group"><br/>
                <button type="submit" class="btn btn-warning btn-rounded"><i class="ik ik-check-circle"></i> {{ __('Enregistrer')}}</button>
            </div>
        </div>
    </div>
</form>