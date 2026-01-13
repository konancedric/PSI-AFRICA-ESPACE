<?php 
    if($dataEntreprise->_tokent_public != "")
    {
        $publicKey_ent = Illuminate\Support\Facades\Crypt::decrypt($dataEntreprise->_tokent_public);
    }
    else
    {
        $publicKey_ent = "veuillez definir une clé public";
    }
    if($dataEntreprise->_tokent_private != "")
    {
        $privateKey_ent = Illuminate\Support\Facades\Crypt::decrypt($dataEntreprise->_tokent_private);
    }
    else
    {
        $privateKey_ent = "veuillez definir une clé privée";
    }
?>
<button class="btn btn-warning btn-sm ml-4" href="#MConfiAccessAPI" data-toggle="modal" data-target="#MConfiAccessAPI" title="Configurer vos accès API"><i class="fa fa-key"></i> CONFIGURER VOS ACCÈS API
</button>
<div class="modal fade edit-layout-modal" id="MConfiAccessAPI" tabindex="-1" role="dialog" aria-labelledby="MConfiAccessAPILabel" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header bg-warning">
                <h5 class="modal-title text-white" id="MConfiAccessAPILabel">
                    <b>CONFIGURER VOS ACCÈS API</b></h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times; </span></button>
            </div>
            <div class="modal-body text-justify">
               <div class="card-body">
                    <form class="form-horizontal row" method="POST" action="{{url('config-api')}}">
                        @csrf
                        <input type="hidden" name="user1d" value="{{Auth::user()->id}}">
                        <input type="hidden" name="id" value="{{$dataEntreprise->id}}">
                        <div class="col-sm-12">
                            <label for="tokentPublic"><b><i class="fa fa-users"></i> Public Key </b></label>
                            <div class="input-group input-group-button">
                                <input type="text" id="tokentPublic" name="tokentPublic" class="form-control" value="{{$publicKey_ent}}" style=" pointer-events: none; background-color: #f2f2f2;" autocomplete="off" />
                                <div class="input-group-append">
                                    <a class="btn btn-dark text-white" id="btnCopueTokentPublic" ><i class="ik ik-copy"></i> </a>
                                </div>
                            </div>
                        </div>
                        <div class="col-sm-12">
                            <label for="tokentPrivate"><b><i class="fa fa-unlock-alt"></i> Private Key </b></label>
                            <div class="input-group input-group-button">
                                <input type="text" id="tokentPrivate" name="tokentPrivate" class="form-control" value="{{$privateKey_ent}}" style=" pointer-events: none; background-color: #f2f2f2;" autocomplete="off" />
                                <div class="input-group-append">
                                    <a class="btn btn-dark text-white" id="btnCopueTokentPrivate" ><i class="ik ik-copy"></i> </a>
                                </div>
                            </div>
                        </div>
                        <div class="col-sm-12">
                            <div class="form-group"><br/>
                                <button type="submit" class="btn btn-primary btn-rounded"><i class="ik ik-check-circle"></i> {{ __('Enregistrer les clés génerées')}}</button>
                                <a class="btn btn-danger btn-rounded text-white" id="boutonGenerateKey"><i class="fa fa-spinner"></i> {{ __('Génerer de nouvelles clés')}}</a>
                            </div>
                        </div>

                        <span class="text-red"><b><i class="fa fa-info-circle"></i> Attention : si vous changez vos clé, vous devrez également mettre à jours vos codes avec les nouvelles clés !</b></span>
                    </form>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-dismiss="modal">{{ __('Fermer')}}</button>
            </div>
        </div>
    </div>
</div>

<script>
    document.getElementById("boutonGenerateKey").addEventListener("click", function() {
        // Générez une clé (vous pouvez utiliser n'importe quelle méthode de génération de clé ici)
        var tokentPublic = generateKey();
        // Obtenez une référence au champ d'entrée
        var champKeyPublic = document.getElementById("tokentPublic");
        // Attribuez la clé générée au champ
        champKeyPublic.value = "sonec_api_" + tokentPublic +"_pu";

        // Générez une clé (vous pouvez utiliser n'importe quelle méthode de génération de clé ici)
        var tokentPrivate = generateKey();
        // Obtenez une référence au champ d'entrée
        var champKeyPrivate = document.getElementById("tokentPrivate");
        // Attribuez la clé générée au champ
        champKeyPrivate.value = "sonec_api_" + tokentPrivate +"_pr";
    });

    // Exemple de génération de clé simple (à personnaliser)
    function generateKey() {
        // Vous pouvez utiliser une logique de génération de clé personnalisée ici
        // Par exemple, générons une clé aléatoire de 16 caractères
        var caracteres = "ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz0123456789_";
        var longueurCle = 32;
        var cle = "";
        for (var i = 0; i < longueurCle; i++) {
            var caractereAleatoire = caracteres.charAt(Math.floor(Math.random() * caracteres.length));
            cle += caractereAleatoire;
        }
        return cle;
    }
</script>

<script>
    document.getElementById("btnCopueTokentPublic").addEventListener("click", function () {
        // Sélectionnez le champ d'entrée
        var tokentPublic = document.getElementById("tokentPublic");

        // Copiez la valeur du champ d'entrée dans le presse-papiers
        tokentPublic.select(); // Sélectionnez tout le texte dans le champ
        document.execCommand("copy"); // Copiez la sélection dans le presse-papiers

        // Désélectionnez le champ d'entrée
        tokentPublic.setSelectionRange(0, 0);
    });
    document.getElementById("btnCopueTokentPrivate").addEventListener("click", function () {
        // Sélectionnez le champ d'entrée
        var tokentPrivate = document.getElementById("tokentPrivate");

        // Copiez la valeur du champ d'entrée dans le presse-papiers
        tokentPrivate.select(); // Sélectionnez tout le texte dans le champ
        document.execCommand("copy"); // Copiez la sélection dans le presse-papiers

        // Désélectionnez le champ d'entrée
        tokentPrivate.setSelectionRange(0, 0);
    });
</script>
