<form class="forms-sample" method="POST" action="{{url('galerie-video/create')}}">
    @csrf
    <div class="row">
        <input type="hidden" name="user1d" value="{{$user1d}}">
        <div class="col-sm-12">
            <div class="form-group">
                <label for="libelle"><i class="fas fa-tags"></i> {{ __('Libelle')}}<span class="text-red">*</span></label>
                <input type="text" class="form-control" id="libelle" name="libelle" placeholder="Libelle" required>
            </div>
        </div>
        <div class="col-sm-12">
            <div class="form-group">
                <label for="save_url"><i class="fas fa-tag"></i> {{ __('Url Youtube')}}<span class="text-red">*</span></label>
                <input type="text" class="form-control" id="save_url" name="save_url" placeholder="Url Youtube" required>
            </div>
        </div>
        <div class="col-sm-12">
            <div class="form-group"><br/>
                <button type="submit" class="btn btn-warning btn-rounded"><i class="ik ik-check-circle"></i> {{ __('Enregistrer')}}</button>
            </div>
        </div>
    </div>
</form>
