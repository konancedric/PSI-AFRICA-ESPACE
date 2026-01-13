<form class="forms-sample" method="POST" action="{{url('categories/create')}}">
    @csrf
    <div class="row">
        <input type="hidden" name="user1d" value="{{$user1d}}">
        <div class="col-sm-8">
            <div class="form-group">
                <label for="libelle"><i class="fas fa-tags"></i> {{ __('Categorie')}}<span class="text-red">*</span></label>
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
