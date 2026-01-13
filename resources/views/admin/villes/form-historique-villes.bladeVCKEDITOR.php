<!-- Charger CKEditor via CDN -->
<script src="https://cdn.ckeditor.com/4.16.2/standard/ckeditor.js"></script>

<div class="car text-justify">
    <form method="post" action="{{url('villes/add-historique')}}" enctype="multipart/form-data" >
        @csrf
        <div class="row">
            <input type="hidden" name="user1d" value="{{$user1d}}">
            <input type="hidden" name="id" value="{{ $tabVilles->id }}">
            <input type="hidden" name="libelle" value="{{ $tabVilles->libelle }}">
            <input type="hidden" name="etat" value="1">
            <div class="col-sm-12">
                <textarea class="form-control" rows="10" height="750px" name="historique" id="historique"></textarea>
            </div>
            <div class="col-sm-12">
                <div class="form-group"><br/>
                    <button type="submit" class="btn btn-warning btn-rounded"><i class="ik ik-check-circle"></i> {{ __('Enregistrer')}}</button>
                </div>
            </div>
        </div>
    </form>
</div>
<script>
  // Initialiser CKEditor
  CKEDITOR.replace('historique');
</script>
