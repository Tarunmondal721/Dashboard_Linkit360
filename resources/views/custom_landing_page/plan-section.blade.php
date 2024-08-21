<section class="pricing-plan bg-gredient3">
    <div class="container our-system">
        <div class="row">
            <div class="col-12">
                <ul class="nav nav-tabs">
                    <li>
                        <a data-toggle="tab" href="#monthly" class="active">{{__('Monthly')}}</a>
                    </li>
                    <li class="annual-billing">
                        <a data-toggle="tab" href="#annually">{{__('Annually')}}</a>
                    </li>
                </ul>
            </div>
            <div class="tab-content">
                <div id="monthly" class="tab-pane in active">
                    <div class="row">
                        @foreach ($plans as $key => $plan)
                            <div class="col-lg-3 col-md-4 col-sm-6 py-2">
                                <div class="plan-2">
                                    <h6 class="text-center">{{ $plan->name }}</h6>
                                    <p class="price">
                                        <sup>{{(env('CURRENCY_SYMBOL') ? env('CURRENCY_SYMBOL') : '$')}}</sup>
                                        {{ number_format($plan->monthly_price) }}
                                        <sub>/ {{__('Month')}}</sub>
                                    </p>
                                    <p class="text-sm font-weight-bold">{{__('Trial : ').$plan->trial_days.__(' Days')}}</p>
                                    <ul class="plan-detail">
                                        <li>{{ ($plan->max_users < 0) ? __('Unlimited'):$plan->max_users }} {{__('Users')}}</li>
                                        <li>{{ ($plan->max_clients < 0) ? __('Unlimited'):$plan->max_clients }} {{__('Clients')}}</li>
                                        <li>{{ ($plan->max_deals < 0) ? __('Unlimited'):$plan->max_deals }} {{__('Deals')}}</li>
                                    </ul>
                                    <a href="{{ route('register') }}" class="button">Active</a>
                                </div>
                            </div>
                        @endforeach
                    </div>
                </div>
                <div id="annually" class="tab-pane in">
                    <div class="row">
                        @foreach ($plans as $key => $plan)
                            <div class="col-lg-3 col-md-4 col-sm-6 py-2">
                                <div class="plan-2">
                                    <h6 class="text-center">{{ $plan->name }}</h6>
                                    <p class="price">
                                        <sup>{{(env('CURRENCY_SYMBOL') ? env('CURRENCY_SYMBOL') : '$')}}</sup>
                                        {{ number_format($plan->annual_price) }}
                                        <sub>/ {{__('Annual')}}</sub>
                                    </p>
                                    <p class="text-sm font-weight-bold">{{__('Trial : ').$plan->trial_days.__(' Days')}}</p>
                                    <ul class="plan-detail">
                                        <li>{{ ($plan->max_users < 0) ? __('Unlimited'):$plan->max_users }} {{__('Users')}}</li>
                                        <li>{{ ($plan->max_clients < 0) ? __('Unlimited'):$plan->max_clients }} {{__('Clients')}}</li>
                                        <li>{{ ($plan->max_deals < 0) ? __('Unlimited'):$plan->max_deals }} {{__('Deals')}}</li>
                                    </ul>
                                    <a href="{{ route('register') }}" class="button">Active</a>
                                </div>
                            </div>
                        @endforeach
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>


<div id="ul-section">
    <ul class="list-group list-group-horizontal tooltip1text" style="z-index:1000;">
        <li class="list-group-item"><button class="btn btn-default" id="delete"><i class="fa fa-trash"></i></button></li>
        <li class="list-group-item"><button class="btn btn-default" onclick="copySection(this)" id="copy_btn"><i class="fa fa-copy"></i></button></li>
        <li class="list-group-item"><a class="btn btn-default handle"><i class="fa fa-arrows"></i></a></li>
    </ul>
</div>


