<a class="page-quick-sidebar-toggler"><i class="icon-login"></i></a>
<div class="page-quick-sidebar-wrapper">
    <div class="page-quick-sidebar">
        <div class="nav-justified">
            <ul class="nav nav-tabs nav-justified">
                <li class="active">
                    <a href="#quick_sidebar_tab_1" data-toggle="tab" style="text-align:left;">
                        KİŞİLER <span class="badge badge-danger"></span>
                    </a>
                </li>
            </ul>
            <div class="tab-content">
                <div class="tab-pane active page-quick-sidebar-chat" id="quick_sidebar_tab_1">
                    <div class="page-quick-sidebar-chat-users" data-rail-color="#ddd" data-wrapper-class="page-quick-sidebar-list">
                        <h3 class="list-heading">PERSONEL</h3>
                        <ul class="media-list list-items">
                            {{--@if(isset(Auth::user()->personeller) )--}}
                                {{--@foreach(Auth::user()->personeller as $personel)--}}

                                    {{--<li class="media {{$personel->id}}">--}}
                                        {{--<div class="media-status">--}}
                                            {{--@if($personel->gelenmesaj>0)--}}
                                                {{--<span class="badge badge-success">{{$personel->gelenmesaj}}</span>--}}
                                            {{--@endif--}}
                                        {{--</div>--}}
                                        {{--<img class="media-object" src="{{ $personel->avatar ? URL::to('assets/images/profilresim/'.$personel->avatar) : URL::to('assets/images/profilresim/test.png') }}">--}}
                                        {{--<div class="media-body">--}}
                                            {{--<h4 class="media-heading">{{$personel->adi_soyadi}}</h4>--}}
                                            {{--<div class="media-heading-sub">--}}
                                                {{--{{$personel->grup->grupadi}}--}}
                                            {{--</div>--}}
                                            {{--<div class="media-heading-small">--}}
                                                {{--{{$personel->online ? 'Çevrimiçi' : (is_null($personel->last_active) ? 'Bağlı Değil' : 'Son Görülme '.BackendController::time_elapsed($personel->last_active)) }}--}}
                                            {{--</div>--}}
                                        {{--</div>--}}
                                    {{--</li>--}}
                                {{--@endforeach--}}
                            {{--@endif--}}
                        </ul>
                        {{--@if(isset(Auth::user()->grup_id) && Auth::user()->grup_id<>19)--}}
                            {{--<h3 class="list-heading">MÜŞTERİ</h3>--}}
                            {{--<ul class="media-list list-items">--}}
                                {{--@if(isset(Auth::user()->musteriler) )--}}
                                    {{--@foreach(Auth::user()->musteriler as $musteri)--}}
                                        {{--<li class="media {{$musteri->id}}">--}}
                                            {{--<div class="media-status">--}}
                                                {{--@if($musteri->gelenmesaj>0)--}}
                                                    {{--<span class="badge badge-success">{{$musteri->gelenmesaj}}</span>--}}
                                                {{--@endif--}}
                                            {{--</div>--}}
                                            {{--<img class="media-object" src="{{ $musteri->avatar ? URL::to('assets/images/profilresim/'.$musteri->avatar) : URL::to('assets/images/profilresim/test.png') }}">--}}
                                            {{--<div class="media-body">--}}
                                                {{--<h4 class="media-heading">{{$musteri->adi_soyadi}}</h4>--}}
                                                {{--<div class="media-heading-sub">--}}
                                                    {{--{{$musteri->cariadi}}--}}
                                                {{--</div>--}}
                                                {{--<div class="media-heading-small">--}}
                                                    {{--{{$musteri->online ? 'Çevrimiçi' : (is_null($musteri->last_active) ? 'Bağlı Değil' : 'Son Görülme '.BackendController::time_elapsed($musteri->last_active)) }}--}}
                                                {{--</div>--}}
                                            {{--</div>--}}
                                        {{--</li>--}}
                                    {{--@endforeach--}}
                                {{--@endif--}}
                            {{--</ul>--}}
                        {{--@endif--}}
                    </div>
                    <div class="page-quick-sidebar-item">
                        <div class="page-quick-sidebar-chat-user" id="chat-app">
                            <div class="page-quick-sidebar-nav">
                                <input id="alici" class="hide" value=""/>
                                <a href="#" class="page-quick-sidebar-back-to-list"><i class="icon-arrow-left"></i>Geri</a>
                                {{--<img id="aliciimg" src="" class="img-circle" style="height: 39px;"/>--}}
                                <span id="aliciadi"></span>
                            </div>
                            <input type="text" class="ileti hide" value=""/>
                            <div id="chat-log" class="page-quick-sidebar-chat-user-messages">

                            </div>
                            <div class="page-quick-sidebar-chat-user-form">
                                <div class="input-group">
                                    <input type="text" class="form-control" id="chat-message" placeholder="Bir mesaj yaz...">
                                    <div class="input-group-btn">
                                        <button type="button" class="btn blue"><i class="icon-paper-clip"></i></button>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="tab-pane page-quick-sidebar-settings" id="quick_sidebar_tab_3">
                    <div class="page-quick-sidebar-settings-list">
                        <h3 class="list-heading">General Settings</h3>
                        <ul class="list-items borderless">
                            <li>
                                Enable Notifications <input type="checkbox" class="make-switch" checked data-size="small" data-on-color="success" data-on-text="ON" data-off-color="default" data-off-text="OFF">
                            </li>
                            <li>
                                Allow Tracking <input type="checkbox" class="make-switch" data-size="small" data-on-color="info" data-on-text="ON" data-off-color="default" data-off-text="OFF">
                            </li>
                            <li>
                                Log Errors <input type="checkbox" class="make-switch" checked data-size="small" data-on-color="danger" data-on-text="ON" data-off-color="default" data-off-text="OFF">
                            </li>
                            <li>
                                Auto Sumbit Issues <input type="checkbox" class="make-switch" data-size="small" data-on-color="warning" data-on-text="ON" data-off-color="default" data-off-text="OFF">
                            </li>
                            <li>
                                Enable SMS Alerts <input type="checkbox" class="make-switch" checked data-size="small" data-on-color="success" data-on-text="ON" data-off-color="default" data-off-text="OFF">
                            </li>
                        </ul>
                        <h3 class="list-heading">System Settings</h3>
                        <ul class="list-items borderless">
                            <li>
                                Security Level
                                <select class="form-control input-inline input-sm input-small">
                                    <option value="1">Normal</option>
                                    <option value="2" selected>Medium</option>
                                    <option value="e">High</option>
                                </select>
                            </li>
                            <li>
                                Failed Email Attempts <input class="form-control input-inline input-sm input-small" value="5"/>
                            </li>
                            <li>
                                Secondary SMTP Port <input class="form-control input-inline input-sm input-small" value="3560"/>
                            </li>
                            <li>
                                Notify On System Error <input type="checkbox" class="make-switch" checked data-size="small" data-on-color="danger" data-on-text="ON" data-off-color="default" data-off-text="OFF">
                            </li>
                            <li>
                                Notify On SMTP Error <input type="checkbox" class="make-switch" checked data-size="small" data-on-color="warning" data-on-text="ON" data-off-color="default" data-off-text="OFF">
                            </li>
                        </ul>
                        <div class="inner-content">
                            <button class="btn btn-success"><i class="icon-settings"></i> Save Changes</button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
