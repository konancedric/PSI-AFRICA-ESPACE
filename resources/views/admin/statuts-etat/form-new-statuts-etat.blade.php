<form class="forms-sample" method="POST" action="{{url('statuts-etat/create')}}">
    @csrf
    <div class="row">
        <input type="hidden" name="user1d" value="{{$user1d}}">
        <div class="col-sm-5">
            <div class="form-group">
                <label for="libelle"><i class="fa fa-tags"></i> {{ __('Statuts')}}<span class="text-red">*</span></label>
                <input type="text" class="form-control" id="libelle" name="libelle" placeholder="Libelle" required>
            </div>
        </div>
        <div class="col-sm-5">
            <div class="form-group">
                <label for="bg_color"><i class="fa fa-tags"></i> {{ __('Couleur')}}<span class="text-red">*</span></label>
                <input type="color" class="form-control" id="bg_color" name="bg_color" placeholder="Libelle" required>
            </div>
        </div>
        <div class="col-sm-2">
            <div class="form-group"><br/>
                <button type="submit" class="btn btn-primary btn-rounded"><i class="fa fa-check-circle"></i> {{ __('Enregistrer')}}</button>
            </div>
        </div>
    </div>
</form>
