<form class="forms-sample" method="POST" action="{{url('partenaires/create')}}" enctype="multipart/form-data">
    @csrf
    <div class="row">
        <input type="hidden" name="user1d" value="{{$user1d}}">
        <div class="col-sm-6">
            <div class="form-group">
                <label for="libelle"><i class="fas fa-user-cog"></i> {{ __('Partenaire')}}<span class="text-red">*</span></label>
                <input type="text" class="form-control" id="libelle" name="libelle" placeholder="Libelle" required>
            </div>
        </div>
        <div class="col-md-6">
            <div class="form-group">
                <label for="img_partenaires"><i class="fa fa-file-image-o"></i> Logotype (jpeg, png, pdf, (taille max 2 Mo))  *</label>
                <input type="file" class="form-control" id="img_partenaires" placeholder="Logotype (jpeg, png, pdf, (taille max 10Mo))" name="img_partenaires" accept=".jpeg, .png, .jpg" required>
            </div>
        </div>
        <div class="col-sm-6">
            <div class="form-group">
                <label for="site_web"><i class="fas fa-spinner"></i> {{ __('Lien web')}}<span class="text-red">*</span></label>
                <input type="url" class="form-control" id="site_web" name="site_web" placeholder="Lien web" required>
            </div>
        </div>
        <div class="col-sm-6">
            <div class="form-group"><br/>
                <button type="submit" class="btn btn-warning btn-rounded"><i class="ik ik-check-circle"></i> {{ __('Enregistrer')}}</button>
            </div>
        </div>
    </div>
</form>
