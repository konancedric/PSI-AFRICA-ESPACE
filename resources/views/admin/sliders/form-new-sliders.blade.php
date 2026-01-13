<form class="forms-sample" method="POST" action="{{url('sliders/create')}}" enctype="multipart/form-data">
    @csrf
    <div class="row">
        <input type="hidden" name="user1d" value="{{$user1d}}">
        <input type="hidden" name="libelle" value="NO">
        <div class="col-md-6">
            <div class="form-group">
                <label for="img_sliders"><i class="fa fa-file-image-o"></i> Logotype (jpeg, png, pdf, (taille max 2 Mo))  *</label>
                <input type="file" class="form-control" id="img_sliders" placeholder="Logotype (jpeg, png, pdf, (taille max 10Mo))" name="img_sliders" accept=".jpeg, .png, .jpg" required>
            </div>
        </div>
        <div class="col-sm-6">
            <div class="form-group"><br/>
                <button type="submit" class="btn btn-warning btn-rounded"><i class="ik ik-check-circle"></i> {{ __('Enregistrer')}}</button>
            </div>
        </div>
    </div>
</form>
