<!doctype html>
<html class="no-js" lang="fr">
    <head>
        <title> {{$dataEntrepriseSearch->denomination}} | @ {{$Compagny}}  | {{ config('app.name') }}</title>
        <meta name="description" content="">
        <meta name="keywords" content="">
        @include('layouts.layouts-css')
    </head>
    <body>
        <!--[if lt IE 8]>
            <p class="browserupgrade">You are using an <strong>outdated</strong> browser. Please <a href="http://browsehappy.com/">upgrade your browser</a> to improve your experience.</p>
        <![endif]-->
        <div class="container-fluid row">
            <div class="col-md-12">
                @include('include.top-header')
            </div>
            <div class="row col-md-1"></div>
            <div class="row col-md-10">
                <div class="col-lg-4 col-md-5">
                    <div class="card">
                        <div class="card-body">
                            <div class="text-center"> 
                                <img src="/upload/entreprise/{{$dataEntrepriseSearch->logo_ent}}" class="rounded-circle" width="150" />
                                <h4 class="card-title mt-10">{{ $dataEntrepriseSearch->denomination}}</h4>
                                <p class="card-subtitle">@<?=$dataEntrepriseSearch->username?></p>
                                <!--<div class="row text-center justify-content-md-center">
                                    <div class="col-4"><a href="javascript:void(0)" class="link"><i class="ik ik-user"></i> <font class="font-medium">254</font></a></div>
                                    <div class="col-4"><a href="javascript:void(0)" class="link"><i class="ik ik-image"></i> <font class="font-medium">54</font></a></div>
                                </div>-->
                            </div>
                        </div>
                        <hr class="mb-0"> 
                        <div class="card-body"> 
                            <small class="text-muted d-block">{{ __('Email')}} </small>
                            <h6>{{ $dataEntrepriseSearch->emailent}}</h6> 
                            <small class="text-muted d-block pt-10">{{ __('Contact')}}</small>
                            <h6>{{ $dataEntrepriseSearch->contact}}</h6> 
                            <small class="text-muted d-block pt-10">{{ __('Adresse')}}</small>
                            <h6>{{ $dataEntrepriseSearch->adresse}}</h6>
                            <div class="map-box">
                                <iframe src="{{ $dataEntrepriseSearch->map}}" width="100%" height="300" frameborder="0" style="border:0" allowfullscreen></iframe>
                            </div> 
                            <small class="text-muted d-block pt-30">{{ __('Réseaux sociaux')}}</small>
                            <br/>
                            <a class="btn btn-icon btn-primary text-white" target="_blank" href="{{ $dataEntrepriseSearch->link_siteweb}}"><i class="fa fa-globe"></i></a>
                            <a class="btn btn-icon btn-facebook text-white" target="_blank" href="{{ $dataEntrepriseSearch->link_facebook}}"><i class="fab fa-facebook-f"></i></a>
                            <a class="btn btn-icon btn-twitter text-white" target="_blank" href="{{ $dataEntrepriseSearch->link_twitter}}"><i class="fab fa-twitter"></i></a>
                            <a class="btn btn-icon btn-linkedin text-white" target="_blank" href="{{ $dataEntrepriseSearch->link_linkedin}}"><i class="fab fa-linkedin"></i></a>
                        </div>
                    </div>
                </div>
                <div class="col-lg-8 col-md-7">
                    <div class="card">
                        <div class="card-footer bg-dark text-white">
                            {{$dataEntrepriseSearch->description}} 
                            <!-- start message area-->
                                @include('include.message')
                            <!-- end message area-->
                        </div>
                        <hr class="mb-0"> 
                        <div class="card-body card-primary">
                            <ul class="nav nav-pills custom-pills" id="pills-tab" role="tablist">
                                <li class="nav-item">
                                    <a class="nav-link active" id="pills-prise-de-rdv-tab" data-toggle="pill" href="#prise-de-rdv" role="tab" aria-controls="pills-prise-de-rdv" aria-selected="true"><i class="fas fa-calendar"></i> Prendre un RDV</a>
                                </li>
                                <li class="nav-item">
                                    <a class="nav-link" id="pills-modifier-rdv-tab" data-toggle="pill" href="#modifier-rdv" role="tab" aria-controls="pills-modifier-rdv" aria-selected="false"><i class="fas fa-calendar-plus"></i> {{ __('Modifier un RDV')}}</a>
                                </li>
                                <!--<li class="nav-item">
                                    <a class="nav-link" id="pills-setting-tab" data-toggle="pill" href="#previous-month" role="tab" aria-controls="pills-setting" aria-selected="false">{{ __('Setting')}}</a>
                                </li>-->
                            </ul>
                            <div class="tab-content" id="pills-tabContent">
                                <div class="tab-pane fade show active" id="prise-de-rdv" role="tabpanel" aria-labelledby="pills-prise-de-rdv-tab">
                                    <div class="card-body">
                                        @include('include.form-new-prise-de-rdv')
                                    </div>
                                </div>
                                <div class="tab-pane fade" id="modifier-rdv" role="tabpanel" aria-labelledby="pills-modifier-rdv-tab">
                                    <div class="card-body">
                                        @include('include.form-update-prise-de-rdv')
                                    </div>
                                </div>
                                <!--<div class="tab-pane fade" id="previous-month" role="tabpanel" aria-labelledby="pills-setting-tab">
                                    <div class="card-body">
                                        <form class="form-horizontal">
                                            <div class="form-group">
                                                <label for="example-name">{{ __('Full Name')}}</label>
                                                <input type="text" placeholder="Johnathan Doe" class="form-control" name="example-name" id="example-name">
                                            </div>
                                            <div class="form-group">
                                                <label for="example-email">{{ __('Email')}}</label>
                                                <input type="email" placeholder="johnathan@admin.com" class="form-control" name="example-email" id="example-email">
                                            </div>
                                            <div class="form-group">
                                                <label for="example-password">{{ __('Password')}}</label>
                                                <input type="password" value="password" class="form-control" name="example-password" id="example-password">
                                            </div>
                                            <div class="form-group">
                                                <label for="example-phone">{{ __('Phone No')}}</label>
                                                <input type="text" placeholder="123 456 7890" id="example-phone" name="example-phone" class="form-control">
                                            </div>
                                            <div class="form-group">
                                                <label for="example-message">{{ __('Message')}}</label>
                                                <textarea name="example-message" name="example-message" rows="5" class="form-control"></textarea>
                                            </div>
                                            <div class="form-group">
                                                <label for="example-country">{{ __('Select Country')}}</label>
                                                <select name="example-message" id="example-message" class="form-control">
                                                    <option>{{ __('London')}}</option>
                                                    <option>{{ __('India')}}</option>
                                                    <option>{{ __('Usa')}}</option>
                                                    <option>{{ __('Canada')}}</option>
                                                    <option>{{ __('Thailand')}}</option>
                                                </select>
                                            </div>
                                            <button class="btn btn-success" type="submit">Update modifier-rdv</button>
                                        </form>
                                    </div>
                                </div>-->
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="row col-md-1"></div>
        </div>
        @include('layouts.layouts-js')
    </body>
</html>

