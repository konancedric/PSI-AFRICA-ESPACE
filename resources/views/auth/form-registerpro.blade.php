<link href="//maxcdn.bootstrapcdn.com/bootstrap/3.3.0/css/bootstrap.min.css" rel="stylesheet" id="bootstrap-css">
<script src="//maxcdn.bootstrapcdn.com/bootstrap/3.3.0/js/bootstrap.min.js"></script>
<script src="//cdnjs.cloudflare.com/ajax/libs/jquery/3.2.1/jquery.min.js"></script>
<!------ Include the above in your HEAD tag ---------->
<style type="text/css">
    .stepwizard-step p 
    {
        margin-top: 0px;
        color:#666;
    }
    .stepwizard-row {
        display: table-row;
    }
    .stepwizard {
        display: table;
        width: 100%;
        position: relative;
    }
    .stepwizard-step button[disabled] {
        /*opacity: 1 !important;
        filter: alpha(opacity=100) !important;*/
    }
    .stepwizard .btn.disabled, .stepwizard .btn[disabled], .stepwizard fieldset[disabled] .btn {
        opacity:1 !important;
        color:#bbb;
    }
    .stepwizard-row:before {
        top: 14px;
        bottom: 0;
        position: absolute;
        content:" ";
        width: 100%;
        height: 1px;
        background-color: #ccc;
        z-index: 0;
    }
    .stepwizard-step {
        display: table-cell;
        text-align: center;
        position: relative;
    }
    .btn-circle {
        width: 30px;
        height: 30px;
        text-align: center;
        padding: 6px 0;
        font-size: 12px;
        line-height: 1.428571429;
        border-radius: 15px;
    }
</style>
    <div class="stepwizard">
        <div class="row setup-panel stepwizard">
            <div class="stepwizard-step col-xs-3"> 
                <a href="#step-1" type="button" class="btn btn-success btn-circle">1</a>
                <p><small>Informations de l'entreprise ou la structure</small></p>
            </div>
            <div class="stepwizard-step col-xs-3"> 
                <a href="#step-2" type="button" class="btn btn-default btn-circle" disabled="disabled">2</a>
                <p><small>Compte iVoire Click</small></p>
            </div>
            <div class="stepwizard-step col-xs-3"> 
                <a href="#step-3" type="button" class="btn btn-default btn-circle" disabled="disabled">3</a>
                <p><small>Compte Administrateur</small></p>
            </div>
        </div>
    </div>
    
    <form action="{{url('register/pro')}}" enctype="multipart/form-data" method="post" role="form">
        @csrf
        <div class="panel panel-primary setup-content" id="step-1">
            <div class="panel-heading">
                 <h3 class="panel-title">Informations du siège de l'entreprise ou la structure</h3>
            </div>
            <div class="panel-body">
                <div class="form-group">
                    <input type="text" class="form-control" placeholder="Denomination de l'entreprise ou la structure" name="denomination" value="{{ old('denomination') }}" required />
                    <i class="fa fa-university"></i>
                    @error('denomination')
                        <span class="invalid-feedback" role="alert">
                            <strong>{{ $message }}</strong>
                        </span>
                    @enderror
                </div>
                <div class="form-group">
                    <input type="email" class="form-control" placeholder="Email de l'entreprise ou la structure" name="emailent" value="{{ old('emailent') }}" required />
                    <i class="fa fa-envelope"></i>
                    @error('emailent')
                        <span class="invalid-feedback" role="alert">
                            <strong>{{ $message }}</strong>
                        </span>
                    @enderror
                </div>
                <div class="form-group">
                    <input type="text" class="form-control" placeholder="Adresse du siège de l'entreprise ou la structure" name="adresse" value="{{ old('adresse') }}" required />
                    <i class="fa fa-map-marker"></i>
                    @error('adresse')
                        <span class="invalid-feedback" role="alert">
                            <strong>{{ $message }}</strong>
                        </span>
                    @enderror
                </div>
                <div class="form-group">
                    <input type="text" class="form-control" placeholder="Contact de l'entreprise ou la structure" name="contact" value="{{ old('contact') }}" required />
                    <i class="ik ik-phone"></i>
                    @error('contact')
                        <span class="invalid-feedback" role="alert">
                            <strong>{{ $message }}</strong>
                        </span>
                    @enderror
                </div>
                <button class="btn btn-primary nextBtn pull-right" type="button"><i class="fa fa-arrow-circle-right"></i> Suivant</button>
            </div>
        </div>
        <div class="panel panel-primary setup-content" id="step-2">
            <div class="panel-heading">
                 <h3 class="panel-title">Compte Ivoire Click</h3>
            </div>
            <div class="panel-body">
                <div class="form-group">
                    <input type="text" class="form-control" placeholder="Nom d'utilisateur - Exemple : ivoireclick" name="username" value="{{ old('username') }}" required>
                    <i class="ik ik-user"></i>
                    @error('username')
                        <span class="invalid-feedback" role="alert">
                            <strong>{{ $message }}</strong>
                        </span>
                    @enderror
                </div>
                <div class="form-group">
                    <label for="logo_ent"><i class="ik ik-file"></i> {{ __('Logo')}} *<span class="text-red"></span></label>
                    <input type="file" class="form-control" id="logo_ent" accept="image/*" name="logo_ent" placeholder="logo" required>
                    @error('logo_ent')
                        <span class="invalid-feedback" role="alert">
                            <strong>{{ $message }}</strong>
                        </span>
                    @enderror
                </div>
                <div class="form-group">
                    <label for="id_ville"><i class="fa fa-tags"></i> {{ __('Villes')}}<span class="text-red">*</span></label>
                    <select class="form-control select2" id="id_ville" name="id_ville" required>
                        <option value="">Selectionnez la categorie</option>
                        @foreach (App\Models\Villes::where('etat', 1)->get() as $tabVilles)
                            <option value="{{ $tabVilles->id }}">{{ $tabVilles->libelle }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="form-group">
                    <label for="id_souscategorie"><i class="fa fa-tags"></i> {{ __('Categories')}}<span class="text-red">*</span></label>
                    <select class="form-control select2" id="id_souscategorie" name="id_souscategorie" required>
                        <option value="">Selectionnez la categorie</option>
                        @foreach (App\Models\SousCategories::where('etat', 1)->get() as $tabCategories)
                            <option value="{{ $tabCategories->id }}">{{ $tabCategories->libelle }}</option>
                        @endforeach
                    </select>
                </div>
               <?php /* <div class="form-group">
                    <label for="id_souscategorie[]"><i class="fa fa-tags"></i> {{ __('Categories')}}<span class="text-red">*</span></label>
                    <select class="form-control select2" id="id_souscategorie" name="id_souscategorie[]" multiple="" required>
                        <option value="">Selectionnez la categorie</option>
                        @foreach (App\Models\SousCategories::where('etat', 1)->get() as $tabCategories)
                            <option value="{{ $tabCategories->id }}">{{ $tabCategories->libelle }}</option>
                        @endforeach
                    </select>
                </div> */ ?>
                <div class="form-group">
                    <label for="description"><i class="fa fa-comments"></i> {{ __('Description')}} *<span class="text-red"></span></label>
                    <textarea type="text" class="form-control row-5" placeholder="Description de l'entreprise ou la structure" name="description" value="{{ old('description') }}" required></textarea>
                </div>
                <button class="btn btn-primary nextBtn pull-right" type="button"><i class="fa fa-arrow-circle-right"></i> Suivant</button>
            </div>
        </div>
        <div class="panel panel-primary setup-content" id="step-3">
            <div class="panel-heading">
                 <h3 class="panel-title">Compte Administrateur</h3>
            </div>
            <div class="panel-body">
                <div class="form-group">
                    <input type="name" class="form-control" placeholder="Nom & Prénom" name="name" value="{{ old('name') }}" required>
                    <i class="ik ik-user"></i>
                    @error('name')
                        <span class="invalid-feedback" role="alert">
                            <strong>{{ $message }}</strong>
                        </span>
                    @enderror
                </div>
                <div class="form-group">
                    <input type="email" class="form-control" placeholder="Email" name="email" value="{{ old('email') }}" required>
                    <i class="fa fa-envelope"></i>
                    @error('email')
                        <span class="invalid-feedback" role="alert">
                            <strong>{{ $message }}</strong>
                        </span>
                    @enderror
                </div>
                <div class="form-group">
                    <input type="password" class="form-control" placeholder="Mot de passe" name="password" required>
                    <i class="ik ik-lock"></i>
                    @error('password')
                        <span class="invalid-feedback" role="alert">
                            <strong>{{ $message }}</strong>
                        </span>
                    @enderror
                </div>
                <div class="form-group">
                    <input type="password" class="form-control" placeholder="Confirmer le mot de passe" name="password_confirmation" required>
                    <i class="ik ik-eye-off"></i>
                    @error('password_confirmation')
                        <span class="invalid-feedback" role="alert">
                            <strong>{{ $message }}</strong>
                        </span>
                    @enderror
                </div>
                <div class="row">
                    <div class="col-12 text-left">
                        <label class="custom-control custom-checkbox">
                            <input type="checkbox" class="custom-control-input" id="item_checkbox" name="item_checkbox" value="option1" required>
                            <span class="custom-control-label">&nbsp;{{ __('I Accept')}} <a href="#">{{ __('Terms and Conditions')}}</a></span>
                        </label>
                    </div>
                </div>
                <div class="sign-btn text-center">
                    <button class="btn btn-custom bg-primary"><i class="fa fa-user-plus"></i> {{ __('Enregistrer')}}</button>
                </div>
            </div>
        </div>
    </form>

<script type="text/javascript">
    $(document).ready(function () {

    var navListItems = $('div.setup-panel div a'),
        allWells = $('.setup-content'),
        allNextBtn = $('.nextBtn');

    allWells.hide();

    navListItems.click(function (e) {
        e.preventDefault();
        var $target = $($(this).attr('href')),
            $item = $(this);

        if (!$item.hasClass('disabled')) {
            navListItems.removeClass('btn-success').addClass('btn-default');
            $item.addClass('btn-success');
            allWells.hide();
            $target.show();
            $target.find('input:eq(0)').focus();
        }
    });

    allNextBtn.click(function () {
        var curStep = $(this).closest(".setup-content"),
            curStepBtn = curStep.attr("id"),
            nextStepWizard = $('div.setup-panel div a[href="#' + curStepBtn + '"]').parent().next().children("a"),
            curInputs = curStep.find("input[type='text'],input[type='url']"),
            isValid = true;

        $(".form-group").removeClass("has-error");
        for (var i = 0; i < curInputs.length; i++) {
            if (!curInputs[i].validity.valid) {
                isValid = false;
                $(curInputs[i]).closest(".form-group").addClass("has-error");
            }
        }

        if (isValid) nextStepWizard.removeAttr('disabled').trigger('click');
    });

    $('div.setup-panel div a.btn-success').trigger('click');
});
</script>
<script type="text/javascript">
    $(document).ready(function() {
        $('#id_ville').select2();
        $('#id_categorie').select2();
    });
</script>