@extends('layouts.main')
@section('title', 'Configuration du compte entreprise')
@section('content')
    <!-- push external head elements to head -->
    <div class="container-fluid">
    	<div class="page-header">
            <div class="row align-ervicems-end">
                <div class="col-lg-8">
                    <div class="page-header-title">
                        <i class="fa fa-university bg-blue"></i>
                        <div class="d-inline">
                            <h5>{{ __('Configuration du compte entreprise')}}</h5>
                            <span>{{ __('Configuration du compte entreprise')}}</span>
                        </div>
                    </div>
                </div>
                <div class="col-lg-4">
                    <nav class="breadcrumb-container" aria-label="breadcrumb">
                        <ol class="breadcrumb">
                            <li class="breadcrumb-ervicem">
                                <a href="{{url('dashboard')}}"><i class="ik ik-tag"></i></a>
                            </li>
                            <li class="breadcrumb-ervicem">
                                <a href="#">{{ __('Configuration du compte entreprise')}}</a>
                            </li>
                        </ol>
                    </nav>
                </div>
            </div>
        </div>
        <div class="row clearfix">
            <!-- start message area-->
            @include('include.message')
            <!-- end message area-->
            <!-- only those have manage_config_ent rendezvous will get access -->
            @can('manage_config_ent')
            <div class="col-md-12">
                <div class="card">
                    <div class="card-header">
                    </div>
                    <div class="card-header bg-dark text-white text-center">
                        PARAMETRER LES INFORMATIONS DE VOTRE ENTREPRISE
                    </div>
                    <div class="card-body">
                        <form class="form-horizontal row" method="POST" action="{{url('configuration')}}">
                            @csrf
                            <input type="hidden" name="user1d" value="{{Auth::user()->id}}">
                            <input type="hidden" name="id" value="{{$dataEntreprise->id}}">
                            <div class="form-group col-md-6">
                                <label for="denomination">{{ __('Denomination')}}</label>
                                <input type="text" placeholder="{{$dataEntreprise->denomination}}" value="{{$dataEntreprise->denomination}}" class="form-control" name="denomination" id="denomination">
                            </div>
                            <div class="form-group col-md-6">
                                <label for="username">{{ __('User Name')}}</label>
                                <input type="text" placeholder="{{$dataEntreprise->username}}" value="{{$dataEntreprise->username}}" class="form-control" name="username" id="username">
                            </div>
                            <div class="form-group col-md-6">
                                <label for="emailent">{{ __('Email')}}</label>
                                <input type="email"  placeholder="{{$dataEntreprise->emailent}}" value="{{$dataEntreprise->emailent}}" class="form-control" name="emailent" id="emailent">
                            </div>
                            <div class="form-group col-md-6">
                                <label for="adresse">{{ __('Adresse')}}</label>
                                <input type="text" placeholder="{{$dataEntreprise->adresse}}" value="{{$dataEntreprise->adresse}}" class="form-control" name="adresse" id="adresse">
                            </div>
                            <div class="form-group col-md-6">
                                <label for="contact">{{ __('Contact')}}</label>
                                <input type="text" placeholder="{{$dataEntreprise->contact}}" value="{{$dataEntreprise->contact}}" class="form-control" name="contact" id="contact">
                            </div>
                            <div class="form-group col-md-6">
                                <label for="map">{{ __('Map')}}</label>
                                <input type="text" placeholder="{{$dataEntreprise->map}}" value="{{$dataEntreprise->map}}" class="form-control" name="map" id="map">
                            </div>
                            <div class="form-group col-md-6">
                                <label for="link_facebook">{{ __('Link Facebook')}}</label>
                                <input type="text" placeholder="{{$dataEntreprise->link_facebook}}" value="{{$dataEntreprise->link_facebook}}" class="form-control" name="link_facebook" id="link_facebook">
                            </div>
                            <div class="form-group col-md-6">
                                <label for="link_linkedin">{{ __('Link Linkedin')}}</label>
                                <input type="text" placeholder="{{$dataEntreprise->link_linkedin}}" value="{{$dataEntreprise->link_linkedin}}" class="form-control" name="link_linkedin" id="link_linkedin">
                            </div>
                            <div class="form-group col-md-6">
                                <label for="link_twitter">{{ __('Link Twitter')}}</label>
                                <input type="text" placeholder="{{$dataEntreprise->link_twitter}}" value="{{$dataEntreprise->link_twitter}}" class="form-control" name="link_twitter" id="link_twitter">
                            </div>
                            <div class="form-group col-md-6">
                                <label for="link_siteweb">{{ __('Link Site Web')}}</label>
                                <input type="text" placeholder="{{$dataEntreprise->link_siteweb}}" value="{{$dataEntreprise->link_siteweb}}" class="form-control" name="link_siteweb" id="link_siteweb">
                            </div>
                            <div class="form-group col-md-6">
                                <label for="bg_color">{{ __('Bg Color')}}</label>
                                <input type="text" placeholder="{{$dataEntreprise->bg_color}}" value="{{$dataEntreprise->bg_color}}" class="form-control" name="bg_color" id="bg_color">
                            </div>
                            <div class="form-group col-md-6">
                                <label for="description">{{ __('Description')}}</label>
                                <textarea name="description" name="description" rows="5" class="form-control" placeholder="{{$dataEntreprise->description}}" >{{$dataEntreprise->description}}</textarea>
                            </div>
                            <div class="col-sm-12">
                                <div class="form-group"><br/>
                                    <button type="submit" class="btn btn-primary btn-rounded"><i class="ik ik-check-circle"></i> {{ __('Mettre à jour')}}</button>
                                    <button type="reset" class="btn btn-dark btn-rounded">{{ __('Annuler')}}</button>
                                </div>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
            @endcan
        </div>
    </div>
    <!-- push external js -->
    @push('script')
        <script src="{{ asset('js/rendezvous.js') }}"></script>
    @endpush
@endsection
