<button class="btn btn-info btn-sm ml-4" href="#MDocLearnAPI" data-toggle="modal" data-target="#MDocLearnAPI" title="Documentation API"><i class="fa fa-book"></i> DOCUMENTATION API
</button>
<div class="modal fade edit-layout-modal" id="MDocLearnAPI" tabindex="-1" role="dialog" aria-labelledby="MDocLearnAPILabel" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header bg-info">
                <h5 class="modal-title text-white" id="MDocLearnAPILabel">
                    <b>DOCUMENTATION API</b></h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times; </span></button>
            </div>
            <div class="modal-body text-justify">
               <div class="card-body">
                    <div class="container-fluid">
                        <div class="page-header">
                            <div class="row align-items-end">
                                <div class="col-lg-8">
                                    <div class="page-header-title">
                                        <i class="fa fa-key bg-blue"></i>
                                        <div class="d-inline">
                                            <h5>{{ __('REST API')}}</h5>
                                            <span>{{ __('Documentation REST API ')}} </span>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <!-- Instruction start -->
                            <div class="col-md-12">
                                <div class="card table-card proj-t-card">
                                    <div class="card-body">
                                            <div class="row">
                                                <div class="col-md-10 p-3">
                                                   la documentation de  notre API sur {{ config('app.name') }}
                                                </div>
                                            </div>
                                        <h6 class="pt-badge bg-red">{{ __('Api')}} </h6>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-12">
                                <div class="card p-3">
                                    <div class="card-header"><h3>{{ __('Available Api Endpoints')}}</h3></div>
                                    <div class="card-body">
                                        <table id="permission_table" class="table">
                                            <thead>
                                                <tr>
                                                    <th>{{ __('Method')}}</th>
                                                    <th>{{ __('URl')}}</th>
                                                    <th>{{ __('Parameters')}}</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                <tr>
                                                    <td><strong class="text-green">POST</strong></td>
                                                    <td>/api/v1/get-ticket/<span class="text-red">any_id_ticket</span>/<span class="text-red">your_private_key</span>/<span class="text-red">your_public_key</span></td>
                                                    <td>
                                                        <span class="text-muted">Note:<b>any_id_ticket</b> is id ticket, you can replace it with any id ticket </span><br/>
                                                        <span class="text-muted">Note:<b>your_public_key</b> is public key, you can replace it with your public key </span><br/>
                                                        <span class="text-muted">Note:<b>your_private_key</b> is private key, you can replace it with your private key </span>
                                                    </td>
                                                </tr>
                                            </tbody>
                                        </table>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-dismiss="modal">{{ __('Fermer')}}</button>
            </div>
        </div>
    </div>
</div>

<script>
    document.getElementById("boutonGenererCle").addEventListener("click", function() {
        // Générez une clé (vous pouvez utiliser n'importe quelle méthode de génération de clé ici)
        var tokentPublic = genererCle();
        // Obtenez une référence au champ d'entrée
        var champCle = document.getElementById("tokentPublic");
        // Attribuez la clé générée au champ
        champCle.value = "sonec_api_" + tokentPublic +"_pu";

        // Générez une clé (vous pouvez utiliser n'importe quelle méthode de génération de clé ici)
        var tokentPrivate = genererCle();
        // Obtenez une référence au champ d'entrée
        var champCle = document.getElementById("tokentPrivate");
        // Attribuez la clé générée au champ
        champCle.value = "sonec_api_" + tokentPrivate +"_pr";
    });

    // Exemple de génération de clé simple (à personnaliser)
    function genererCle() {
        // Vous pouvez utiliser une logique de génération de clé personnalisée ici
        // Par exemple, générons une clé aléatoire de 16 caractères
        var caracteres = "ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz0123456789_#@";
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
