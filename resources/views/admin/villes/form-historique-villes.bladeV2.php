<div class="car text-justify">
    <form enctype="multipart/form-data" method="post" action="{{url('villes/add-historique')}}" >
        
        <div class="row">
            <input type="hidden" name="user1d" value="{{$user1d}}">
            <input type="hidden" name="id" value="{{ $tabVilles->id }}">
            <input type="hidden" name="libelle" value="{{ $tabVilles->libelle }}">
            <input type="hidden" name="etat" value="1">
            <div class="col-sm-12">
                <!-- <textarea class="form-control html-editor" rows="10" height="750px"class="form-control html-editor"></textarea>
                            Votre formulaire avec la zone de texte -->
                 <!--<textarea id="myTextarea"></textarea>-->
                 <textarea id="myTextarea" class="form-control" name="historique"></textarea>
            </div>
            <div class="col-sm-12">
                <div class="form-group"><br/>
                    <button type="submit" class="btn btn-warning btn-rounded"><i class="ik ik-check-circle"></i> {{ __('Enregistrer')}}</button>
                </div>
            </div>
        </div>
    </form>
</div>