@extends('layouts.admin')

@section('title')
     {{ __("ROI Report") }}
@endsection
@section('pagetytle')
     {{ __("Monitor ADs By Operator") }}
@endsection
@section('content')
        
        @include('analytic.partials.filterRoiReport')

        <div class="card mt-0 roiDiv">
          <div class="table-responsive">
            <table class="table table-sm table-bordered table-stripped m-0 font-13 text-dark" id="roiTbl">
              <input type="hidden" id="last_week" value="">
              <input type="hidden" id="second_last_week" value="">
              <tbody>
                <tr style="background: #FFFFD5;">
                  <td colspan="1" style="background: transparent;">&nbsp;</td>
                  <td colspan="3" style="background: #FCE4D6;" class="text-center">WEEK(Nov 7-13) </td>
                  <td colspan="3" style="background: #FCE4D6;" class="text-center">WEEK(Nov 14-20) </td>
                  <td style="background: #F6D6C3;">&nbsp;</td>
                  <td style="background: #F6D6C3;">&nbsp;</td>
                  <td>&nbsp;</td>
                  <td colspan="2" style="background: #FFE699;">&nbsp;</td>
                  <td colspan="3" class="bg-success text-center">Comparison</td>
                  <td colspan="11">&nbsp;</td>
                </tr>
                <tr style="background: #FFFFD5;">
                  <td>Country Operator</td>
                  <td style="background: #FCE4D6;">Total MO</td>
                  <td style="background: #FCE4D6;">Ad Cost</td>
                  <td style="background: #FCE4D6;">CP Revenue</td>
                  <td style="background: #FCE4D6;">Total MO</td>
                  <td style="background: #FCE4D6;">Ad Cost</td>
                  <td style="background: #FCE4D6;">CP Revenue</td>
                  <td style="background: #F6D6C3;">PnL</td>
                  <td style="background: #F6D6C3;">Unreg</td>
                  <td>Month-end spending</td>
                  <td style="background: #FFE699;">WAKI's gross revenue</td>
                  <td style="background: #FFE699;">Month-end</td>
                  <td class="bg-success">MO</td>
                  <td class="bg-success">Cost</td>
                  <td class="bg-success">Rev.</td>
                  <td>Weekly caps</td>
                  <td>% caps</td>
                  <td>Price/MO</td>
                  <td>Monthly ARPU</td>
                  <td>ROI</td>
                  <td>Active subs.</td>
                  <td>Avr. Daily revenue</td>
                  <td>Avr. Daily cost;</td>
                  <td>Avr. Daily PnL</td>
                  <td>Avr. Daily MO</td>
                  <td>Avr. Unreg daily</td>
                </tr>

                <tr style="background: #FFFFD5;">
                  <td class="font-weight-bold">Total All</td>
                  <td class="mo_total_0 font-weight-bold" style="background: #FCE4D6;">336,608.00</td>
                  <td class="cost_total_0 font-weight-bold" style="background: #FCE4D6;">19,213.17</td>
                  <td class="rev_total_0 font-weight-bold" style="background: #FCE4D6;">$99,132.16</td>
                  <td class="mo_total_1 font-weight-bold" style="background: #FCE4D6;">27,678.00</td>
                  <td class="cost_total_1 font-weight-bold" style="background: #FCE4D6;">$1,712.32</td>
                  <td class="rev_total_1 font-weight-bold" style="background: #FCE4D6;">$11,287.69</td>
                  <td class="pnl_total font-weight-bold" style="background: #F6D6C3;">$6,833.12</td>
                  <td class="unreg_total font-weight-bold" style="background: #F6D6C3;">235,463.00</td>
                  <td class="month_end_spending_total font-weight-bold">$20,925.49</td>
                  <td class="waki_gross_rev_total font-weight-bold" style="background: #FFE699;">$513.71</td>
                  <td class="month_end_total font-weight-bold" style="background: #FFE699;">$154.10</td>
                  <td class="bg-success comparison_mo_total font-weight-bold">-1,116.16%</td>
                  <td class="bg-success comparison_cost_total font-weight-bold">-1,022.05%</td>
                  <td class="bg-success comparison_rev_total font-weight-bold">-778.23%</td>
                  <td class="weekly_caps_total font-weight-bold">$0.00</td>
                  <td class="percent_caps_total font-weight-bold">0.00</td>
                  <td class="roiprice_mo_total font-weight-bold">$0.06</td>
                  <td class="monthly_arpu_total font-weight-bold">$1.75</td>
                  <td class="roi_total font-weight-bold">0.04</td>
                  <td class="subs_total font-weight-bold">27,678.00</td>
                  <td class="average_daily_rev_total font-weight-bold">$14,161.74</td>
                  <td class="average_daily_cost_total font-weight-bold">$2,744.74</td>
                  <td class="average_daily_pnl_total font-weight-bold">$5.16</td>
                  <td class="average_daily_mo_total font-weight-bold">48,086.84</td>
                  <td class="average_daily_unreg_total font-weight-bold">33,637.57</td>
                </tr>

                <tr class="roiRow">
                  <td>PK - Jazz</td>

                  <td class="mo_data_0">0.00</td>
                  <td class="cost_data_0">0.00</td>
                  <td class="rev_data_0">10,116.10</td>
                  <td class="mo_data_1">0.00</td>
                  <td class="cost_data_1">0.00</td>
                  <td class="rev_data_1">1,474.71</td>

                  <td class="pnl_data">902.52</td>

                  <td class="unreg_data">593.00</td>

                  <td class="month_end_spending_data">0.00</td>

                  <td class="waki_gross_rev_data">0.00</td>

                  <td class="month_end_data">0.00</td>

                  <td class="comparison_mo_data">100.00</td>
                  <td class="comparison_cost_data">100.00</td>
                  <td class="comparison_rev_data">-585.97</td>
                  <td class="weekly_caps_data">0</td>
                  <td class="percent_caps_data">0</td>


                  <td class="roiprice_mo_data">0</td>
                  <td class="monthly_arpu_data">0</td>
                  <td class="roi_data_1">0</td>
                  <td class="subs_data">0</td>
                  <td class="average_daily_rev_data">1,445.16</td>
                  <td class="average_daily_cost_data">0</td>
                  <td class="average_daily_pnl_data">0</td>
                  <td class="average_daily_mo_data">0</td>
                  <td class="average_daily_unreg_data">84.71</td>
                </tr>
                <tr class="roiRow">
                  <td>ID - Xlaxiata</td>

                  <td class="mo_data_0">0.00</td>
                  <td class="cost_data_0">0.00</td>
                  <td class="rev_data_0">12,527.97</td>
                  <td class="mo_data_1">0.00</td>
                  <td class="cost_data_1">0.00</td>
                  <td class="rev_data_1">1,404.16</td>

                  <td class="pnl_data">1,104.60</td>

                  <td class="unreg_data">28.00</td>

                  <td class="month_end_spending_data">0.00</td>

                  <td class="waki_gross_rev_data">0.00</td>

                  <td class="month_end_data">0.00</td>

                  <td class="comparison_mo_data">100.00</td>
                  <td class="comparison_cost_data">100.00</td>
                  <td class="comparison_rev_data">-792.21</td>
                  <td class="weekly_caps_data">0</td>
                  <td class="percent_caps_data">0</td>


                  <td class="roiprice_mo_data">0</td>
                  <td class="monthly_arpu_data">0</td>
                  <td class="roi_data_1">0</td>
                  <td class="subs_data">0</td>
                  <td class="average_daily_rev_data">1,789.71</td>
                  <td class="average_daily_cost_data">0</td>
                  <td class="average_daily_pnl_data">0</td>
                  <td class="average_daily_mo_data">0</td>
                  <td class="average_daily_unreg_data">4.00</td>
                </tr>
                <tr class="roiRow">
                  <td>ID - Id-extravaganza-linkit </td>

                  <td class="mo_data_0">0.00</td>
                  <td class="cost_data_0">0.00</td>
                  <td class="rev_data_0">7,954.02</td>
                  <td class="mo_data_1">0.00</td>
                  <td class="cost_data_1">0.00</td>
                  <td class="rev_data_1">1,049.12</td>

                  <td class="pnl_data">755.37</td>

                  <td class="unreg_data">4,801.00</td>

                  <td class="month_end_spending_data">0.00</td>

                  <td class="waki_gross_rev_data">0.00</td>

                  <td class="month_end_data">0.00</td>

                  <td class="comparison_mo_data">100.00</td>
                  <td class="comparison_cost_data">100.00</td>
                  <td class="comparison_rev_data">-658.16</td>
                  <td class="weekly_caps_data">0</td>
                  <td class="percent_caps_data">0</td>


                  <td class="roiprice_mo_data">0</td>
                  <td class="monthly_arpu_data">0</td>
                  <td class="roi_data_1">0</td>
                  <td class="subs_data">0</td>
                  <td class="average_daily_rev_data">1,136.29</td>
                  <td class="average_daily_cost_data">0</td>
                  <td class="average_daily_pnl_data">0</td>
                  <td class="average_daily_mo_data">0</td>
                  <td class="average_daily_unreg_data">685.86</td>
                </tr>
                <tr class="roiRow">
                  <td>ID - Id-telkomsel-mks</td>

                  <td class="mo_data_0">0.00</td>
                  <td class="cost_data_0">0.00</td>
                  <td class="rev_data_0">9,058.86</td>
                  <td class="mo_data_1">0.00</td>
                  <td class="cost_data_1">0.00</td>
                  <td class="rev_data_1">872.03</td>

                  <td class="pnl_data">715.07</td>

                  <td class="unreg_data">29,053.00</td>

                  <td class="month_end_spending_data">0.00</td>

                  <td class="waki_gross_rev_data">0.00</td>

                  <td class="month_end_data">0.00</td>

                  <td class="comparison_mo_data">100.00</td>
                  <td class="comparison_cost_data">100.00</td>
                  <td class="comparison_rev_data">-938.82</td>
                  <td class="weekly_caps_data">0</td>
                  <td class="percent_caps_data">0</td>


                  <td class="roiprice_mo_data">0</td>
                  <td class="monthly_arpu_data">0</td>
                  <td class="roi_data_1">0</td>
                  <td class="subs_data">0</td>
                  <td class="average_daily_rev_data">1,294.12</td>
                  <td class="average_daily_cost_data">0</td>
                  <td class="average_daily_pnl_data">0</td>
                  <td class="average_daily_mo_data">0</td>
                  <td class="average_daily_unreg_data">4,150.43</td>
                </tr>
                <tr class="roiRow">
                  <td>PK - Jazz-ev</td>

                  <td class="mo_data_0">1.00</td>
                  <td class="cost_data_0">0.00</td>
                  <td class="rev_data_0">5,847.36</td>
                  <td class="mo_data_1">0.00</td>
                  <td class="cost_data_1">0.00</td>
                  <td class="rev_data_1">806.73</td>

                  <td class="pnl_data">580.84</td>

                  <td class="unreg_data">560.00</td>

                  <td class="month_end_spending_data">0.00</td>

                  <td class="waki_gross_rev_data">0.00</td>

                  <td class="month_end_data">0.00</td>

                  <td class="comparison_mo_data">100.00</td>
                  <td class="comparison_cost_data">100.00</td>
                  <td class="comparison_rev_data">-624.82</td>
                  <td class="weekly_caps_data">0</td>
                  <td class="percent_caps_data">0</td>


                  <td class="roiprice_mo_data">0</td>
                  <td class="monthly_arpu_data">0</td>
                  <td class="roi_data_1">0</td>
                  <td class="subs_data">0</td>
                  <td class="average_daily_rev_data">835.34</td>
                  <td class="average_daily_cost_data">0</td>
                  <td class="average_daily_pnl_data">0</td>
                  <td class="average_daily_mo_data">0.14</td>
                  <td class="average_daily_unreg_data">80.00</td>
                </tr>
                <tr class="roiRow">
                  <td>ID - Smartfren</td>

                  <td class="mo_data_0">118,842.00</td>
                  <td class="cost_data_0">3,921.11</td>
                  <td class="rev_data_0">4,333.22</td>
                  <td class="mo_data_1">11,638.00</td>
                  <td class="cost_data_1">394.61</td>
                  <td class="rev_data_1">634.53</td>

                  <td class="pnl_data">125.70</td>

                  <td class="unreg_data">125,304.00</td>

                  <td class="month_end_spending_data">4,315.72</td>

                  <td class="waki_gross_rev_data">118.38</td>

                  <td class="month_end_data">35.51</td>

                  <td class="comparison_mo_data">-921.15</td>
                  <td class="comparison_cost_data">-893.67</td>
                  <td class="comparison_rev_data">-582.90</td>
                  <td class="weekly_caps_data">0</td>
                  <td class="percent_caps_data">0</td>


                  <td class="roiprice_mo_data">0.03</td>
                  <td class="monthly_arpu_data">0.2337</td>
                  <td class="roi_data_1">0.15</td>
                  <td class="subs_data">11,638.00</td>
                  <td class="average_daily_rev_data">619.03</td>
                  <td class="average_daily_cost_data">560.16</td>
                  <td class="average_daily_pnl_data">1.11</td>
                  <td class="average_daily_mo_data">16,977.43</td>
                  <td class="average_daily_unreg_data">17,900.57</td>
                </tr>
                <tr class="roiRow">
                  <td>OM - Omn-omantel-linkit</td>

                  <td class="mo_data_0">649.00</td>
                  <td class="cost_data_0">1,190.35</td>
                  <td class="rev_data_0">2,367.15</td>
                  <td class="mo_data_1">111.00</td>
                  <td class="cost_data_1">198.33</td>
                  <td class="rev_data_1">518.49</td>

                  <td class="pnl_data">226.83</td>

                  <td class="unreg_data">724.00</td>

                  <td class="month_end_spending_data">1,388.68</td>

                  <td class="waki_gross_rev_data">59.50</td>

                  <td class="month_end_data">17.85</td>

                  <td class="comparison_mo_data">-484.68</td>
                  <td class="comparison_cost_data">-500.19</td>
                  <td class="comparison_rev_data">-356.55</td>
                  <td class="weekly_caps_data">0</td>
                  <td class="percent_caps_data">0</td>


                  <td class="roiprice_mo_data">1.79</td>
                  <td class="monthly_arpu_data">20.0188</td>
                  <td class="roi_data_1">0.09</td>
                  <td class="subs_data">111.00</td>
                  <td class="average_daily_rev_data">338.16</td>
                  <td class="average_daily_cost_data">170.05</td>
                  <td class="average_daily_pnl_data">1.99</td>
                  <td class="average_daily_mo_data">92.71</td>
                  <td class="average_daily_unreg_data">103.43</td>
                </tr>
                <tr class="roiRow">
                  <td>ID - Waki-tsel-telesat</td>

                  <td class="mo_data_0">30,370.00</td>
                  <td class="cost_data_0">1,725.07</td>
                  <td class="rev_data_0">5,302.66</td>
                  <td class="mo_data_1">1,121.00</td>
                  <td class="cost_data_1">63.78</td>
                  <td class="rev_data_1">514.79</td>

                  <td class="pnl_data">358.35</td>

                  <td class="unreg_data">6,688.00</td>

                  <td class="month_end_spending_data">1,788.85</td>

                  <td class="waki_gross_rev_data">19.13</td>

                  <td class="month_end_data">5.74</td>

                  <td class="comparison_mo_data">-2,609.19</td>
                  <td class="comparison_cost_data">-2,604.72</td>
                  <td class="comparison_rev_data">-930.07</td>
                  <td class="weekly_caps_data">0</td>
                  <td class="percent_caps_data">0</td>


                  <td class="roiprice_mo_data">0.06</td>
                  <td class="monthly_arpu_data">1.9681</td>
                  <td class="roi_data_1">0.03</td>
                  <td class="subs_data">1,121.00</td>
                  <td class="average_daily_rev_data">757.52</td>
                  <td class="average_daily_cost_data">246.44</td>
                  <td class="average_daily_pnl_data">3.07</td>
                  <td class="average_daily_mo_data">4,338.57</td>
                  <td class="average_daily_unreg_data">955.43</td>
                </tr>
                <tr class="roiRow">
                  <td>ID - Pass-tsel-telesat</td>

                  <td class="mo_data_0">51,686.00</td>
                  <td class="cost_data_0">2,417.66</td>
                  <td class="rev_data_0">6,334.06</td>
                  <td class="mo_data_1">3,396.00</td>
                  <td class="cost_data_1">136.06</td>
                  <td class="rev_data_1">511.42</td>

                  <td class="pnl_data">283.30</td>

                  <td class="unreg_data">8,807.00</td>

                  <td class="month_end_spending_data">2,553.72</td>

                  <td class="waki_gross_rev_data">40.82</td>

                  <td class="month_end_data">12.25</td>

                  <td class="comparison_mo_data">-1,421.97</td>
                  <td class="comparison_cost_data">-1,676.91</td>
                  <td class="comparison_rev_data">-1,138.52</td>
                  <td class="weekly_caps_data">0</td>
                  <td class="percent_caps_data">0</td>


                  <td class="roiprice_mo_data">0.04</td>
                  <td class="monthly_arpu_data">0.6454</td>
                  <td class="roi_data_1">0.06</td>
                  <td class="subs_data">3,396.00</td>
                  <td class="average_daily_rev_data">904.87</td>
                  <td class="average_daily_cost_data">345.38</td>
                  <td class="average_daily_pnl_data">2.62</td>
                  <td class="average_daily_mo_data">7,383.71</td>
                  <td class="average_daily_unreg_data">1,258.14</td>
                </tr>
                <tr class="roiRow">
                  <td>ID - Kb-tsel-telesat</td>

                  <td class="mo_data_0">20,612.00</td>
                  <td class="cost_data_0">1,195.73</td>
                  <td class="rev_data_0">3,429.15</td>
                  <td class="mo_data_1">2,341.00</td>
                  <td class="cost_data_1">122.72</td>
                  <td class="rev_data_1">373.65</td>

                  <td class="pnl_data">183.67</td>

                  <td class="unreg_data">3,838.00</td>

                  <td class="month_end_spending_data">1,318.45</td>

                  <td class="waki_gross_rev_data">36.82</td>

                  <td class="month_end_data">11.04</td>

                  <td class="comparison_mo_data">-780.48</td>
                  <td class="comparison_cost_data">-874.36</td>
                  <td class="comparison_rev_data">-817.75</td>
                  <td class="weekly_caps_data">0</td>
                  <td class="percent_caps_data">0</td>


                  <td class="roiprice_mo_data">0.05</td>
                  <td class="monthly_arpu_data">0.6840</td>
                  <td class="roi_data_1">0.08</td>
                  <td class="subs_data">2,341.00</td>
                  <td class="average_daily_rev_data">489.88</td>
                  <td class="average_daily_cost_data">170.82</td>
                  <td class="average_daily_pnl_data">2.87</td>
                  <td class="average_daily_mo_data">2,944.57</td>
                  <td class="average_daily_unreg_data">548.29</td>
                </tr>
                <tr class="roiRow">
                  <td>ID - Id-surat-sakit</td>

                  <td class="mo_data_0">0.00</td>
                  <td class="cost_data_0">0.00</td>
                  <td class="rev_data_0">2,470.00</td>
                  <td class="mo_data_1">0.00</td>
                  <td class="cost_data_1">0.00</td>
                  <td class="rev_data_1">347.34</td>

                  <td class="pnl_data">250.09</td>

                  <td class="unreg_data">0.00</td>

                  <td class="month_end_spending_data">0.00</td>

                  <td class="waki_gross_rev_data">0.00</td>

                  <td class="month_end_data">0.00</td>

                  <td class="comparison_mo_data">100.00</td>
                  <td class="comparison_cost_data">100.00</td>
                  <td class="comparison_rev_data">-611.11</td>
                  <td class="weekly_caps_data">0</td>
                  <td class="percent_caps_data">0</td>


                  <td class="roiprice_mo_data">0</td>
                  <td class="monthly_arpu_data">0</td>
                  <td class="roi_data_1">0</td>
                  <td class="subs_data">0</td>
                  <td class="average_daily_rev_data">352.86</td>
                  <td class="average_daily_cost_data">0</td>
                  <td class="average_daily_pnl_data">0</td>
                  <td class="average_daily_mo_data">0</td>
                  <td class="average_daily_unreg_data">0</td>
                </tr>
                <tr class="roiRow">
                  <td>ID - Three</td>

                  <td class="mo_data_0">0.00</td>
                  <td class="cost_data_0">0.00</td>
                  <td class="rev_data_0">3,285.50</td>
                  <td class="mo_data_1">0.00</td>
                  <td class="cost_data_1">0.00</td>
                  <td class="rev_data_1">301.99</td>

                  <td class="pnl_data">253.67</td>

                  <td class="unreg_data">0.00</td>

                  <td class="month_end_spending_data">0.00</td>

                  <td class="waki_gross_rev_data">0.00</td>

                  <td class="month_end_data">0.00</td>

                  <td class="comparison_mo_data">100.00</td>
                  <td class="comparison_cost_data">100.00</td>
                  <td class="comparison_rev_data">-987.94</td>
                  <td class="weekly_caps_data">0</td>
                  <td class="percent_caps_data">0</td>


                  <td class="roiprice_mo_data">0</td>
                  <td class="monthly_arpu_data">0</td>
                  <td class="roi_data_1">0</td>
                  <td class="subs_data">0</td>
                  <td class="average_daily_rev_data">469.36</td>
                  <td class="average_daily_cost_data">0</td>
                  <td class="average_daily_pnl_data">0</td>
                  <td class="average_daily_mo_data">0</td>
                  <td class="average_daily_unreg_data">0</td>
                </tr>
                <tr class="roiRow">
                  <td>ID - Yatta-tsel-telesat</td>

                  <td class="mo_data_0">36,850.00</td>
                  <td class="cost_data_0">1,891.43</td>
                  <td class="rev_data_0">2,594.32</td>
                  <td class="mo_data_1">1,900.00</td>
                  <td class="cost_data_1">94.38</td>
                  <td class="rev_data_1">240.33</td>

                  <td class="pnl_data">78.66</td>

                  <td class="unreg_data">5,647.00</td>

                  <td class="month_end_spending_data">1,985.81</td>

                  <td class="waki_gross_rev_data">28.31</td>

                  <td class="month_end_data">8.49</td>

                  <td class="comparison_mo_data">-1,839.47</td>
                  <td class="comparison_cost_data">-1,904.06</td>
                  <td class="comparison_rev_data">-979.48</td>
                  <td class="weekly_caps_data">0</td>
                  <td class="percent_caps_data">0</td>


                  <td class="roiprice_mo_data">0.05</td>
                  <td class="monthly_arpu_data">0.5421</td>
                  <td class="roi_data_1">0.09</td>
                  <td class="subs_data">1,900.00</td>
                  <td class="average_daily_rev_data">370.62</td>
                  <td class="average_daily_cost_data">270.20</td>
                  <td class="average_daily_pnl_data">1.37</td>
                  <td class="average_daily_mo_data">5,264.29</td>
                  <td class="average_daily_unreg_data">806.71</td>
                </tr>
                <tr class="roiRow">
                  <td>ID - Id-xl-yatta</td>

                  <td class="mo_data_0">0.00</td>
                  <td class="cost_data_0">0.00</td>
                  <td class="rev_data_0">1,596.97</td>
                  <td class="mo_data_1">0.00</td>
                  <td class="cost_data_1">0.00</td>
                  <td class="rev_data_1">203.74</td>

                  <td class="pnl_data">151.78</td>

                  <td class="unreg_data">15.00</td>

                  <td class="month_end_spending_data">0.00</td>

                  <td class="waki_gross_rev_data">0.00</td>

                  <td class="month_end_data">0.00</td>

                  <td class="comparison_mo_data">100.00</td>
                  <td class="comparison_cost_data">100.00</td>
                  <td class="comparison_rev_data">-683.84</td>
                  <td class="weekly_caps_data">0</td>
                  <td class="percent_caps_data">0</td>


                  <td class="roiprice_mo_data">0</td>
                  <td class="monthly_arpu_data">0</td>
                  <td class="roi_data_1">0</td>
                  <td class="subs_data">0</td>
                  <td class="average_daily_rev_data">228.14</td>
                  <td class="average_daily_cost_data">0</td>
                  <td class="average_daily_pnl_data">0</td>
                  <td class="average_daily_mo_data">0</td>
                  <td class="average_daily_unreg_data">2.14</td>
                </tr>
                <tr class="roiRow">
                  <td>PH - Smartp</td>

                  <td class="mo_data_0">1,142.00</td>
                  <td class="cost_data_0">219.68</td>
                  <td class="rev_data_0">1,489.84</td>
                  <td class="mo_data_1">121.00</td>
                  <td class="cost_data_1">16.05</td>
                  <td class="rev_data_1">192.13</td>

                  <td class="pnl_data">141.50</td>

                  <td class="unreg_data">172.00</td>

                  <td class="month_end_spending_data">235.73</td>

                  <td class="waki_gross_rev_data">4.82</td>

                  <td class="month_end_data">1.44</td>

                  <td class="comparison_mo_data">-843.80</td>
                  <td class="comparison_cost_data">-1,268.72</td>
                  <td class="comparison_rev_data">-675.44</td>
                  <td class="weekly_caps_data">0</td>
                  <td class="percent_caps_data">0</td>


                  <td class="roiprice_mo_data">0.13</td>
                  <td class="monthly_arpu_data">6.8050</td>
                  <td class="roi_data_1">0.02</td>
                  <td class="subs_data">121.00</td>
                  <td class="average_daily_rev_data">212.83</td>
                  <td class="average_daily_cost_data">31.38</td>
                  <td class="average_daily_pnl_data">6.78</td>
                  <td class="average_daily_mo_data">163.14</td>
                  <td class="average_daily_unreg_data">24.57</td>
                </tr>
                <tr class="roiRow">
                  <td>ID - Linkit-tsel-telesat</td>

                  <td class="mo_data_0">25,326.00</td>
                  <td class="cost_data_0">1,323.99</td>
                  <td class="rev_data_0">1,699.04</td>
                  <td class="mo_data_1">1,368.00</td>
                  <td class="cost_data_1">66.33</td>
                  <td class="rev_data_1">158.15</td>

                  <td class="pnl_data">47.53</td>

                  <td class="unreg_data">0.00</td>

                  <td class="month_end_spending_data">1,390.32</td>

                  <td class="waki_gross_rev_data">19.90</td>

                  <td class="month_end_data">5.97</td>

                  <td class="comparison_mo_data">-1,751.32</td>
                  <td class="comparison_cost_data">-1,896.07</td>
                  <td class="comparison_rev_data">-974.35</td>
                  <td class="weekly_caps_data">0</td>
                  <td class="percent_caps_data">0</td>


                  <td class="roiprice_mo_data">0.05</td>
                  <td class="monthly_arpu_data">0.4954</td>
                  <td class="roi_data_1">0.10</td>
                  <td class="subs_data">1,368.00</td>
                  <td class="average_daily_rev_data">242.72</td>
                  <td class="average_daily_cost_data">189.14</td>
                  <td class="average_daily_pnl_data">1.28</td>
                  <td class="average_daily_mo_data">3,618.00</td>
                  <td class="average_daily_unreg_data">0</td>
                </tr>
                <tr class="roiRow">
                  <td>ID - Id-isat-yatta</td>

                  <td class="mo_data_0">0.00</td>
                  <td class="cost_data_0">0.00</td>
                  <td class="rev_data_0">895.96</td>
                  <td class="mo_data_1">0.00</td>
                  <td class="cost_data_1">0.00</td>
                  <td class="rev_data_1">123.09</td>

                  <td class="pnl_data">91.70</td>

                  <td class="unreg_data">244.00</td>

                  <td class="month_end_spending_data">0.00</td>

                  <td class="waki_gross_rev_data">0.00</td>

                  <td class="month_end_data">0.00</td>

                  <td class="comparison_mo_data">100.00</td>
                  <td class="comparison_cost_data">100.00</td>
                  <td class="comparison_rev_data">-627.90</td>
                  <td class="weekly_caps_data">0</td>
                  <td class="percent_caps_data">0</td>


                  <td class="roiprice_mo_data">0</td>
                  <td class="monthly_arpu_data">0</td>
                  <td class="roi_data_1">0</td>
                  <td class="subs_data">0</td>
                  <td class="average_daily_rev_data">127.99</td>
                  <td class="average_daily_cost_data">0</td>
                  <td class="average_daily_pnl_data">0</td>
                  <td class="average_daily_mo_data">0</td>
                  <td class="average_daily_unreg_data">34.86</td>
                </tr>
                <tr class="roiRow">
                  <td>ID - Id-oxford-airpay</td>

                  <td class="mo_data_0">0.00</td>
                  <td class="cost_data_0">0.00</td>
                  <td class="rev_data_0">902.38</td>
                  <td class="mo_data_1">0.00</td>
                  <td class="cost_data_1">0.00</td>
                  <td class="rev_data_1">113.06</td>

                  <td class="pnl_data">92.71</td>

                  <td class="unreg_data">1,739.00</td>

                  <td class="month_end_spending_data">0.00</td>

                  <td class="waki_gross_rev_data">0.00</td>

                  <td class="month_end_data">0.00</td>

                  <td class="comparison_mo_data">100.00</td>
                  <td class="comparison_cost_data">100.00</td>
                  <td class="comparison_rev_data">-698.12</td>
                  <td class="weekly_caps_data">0</td>
                  <td class="percent_caps_data">0</td>


                  <td class="roiprice_mo_data">0</td>
                  <td class="monthly_arpu_data">0</td>
                  <td class="roi_data_1">0</td>
                  <td class="subs_data">0</td>
                  <td class="average_daily_rev_data">128.91</td>
                  <td class="average_daily_cost_data">0</td>
                  <td class="average_daily_pnl_data">0</td>
                  <td class="average_daily_mo_data">0</td>
                  <td class="average_daily_unreg_data">248.43</td>
                </tr>
                <tr class="roiRow">
                  <td>AE - Uae-etisalat-linkit</td>

                  <td class="mo_data_0">4.00</td>
                  <td class="cost_data_0">15.60</td>
                  <td class="rev_data_0">1,025.24</td>
                  <td class="mo_data_1">2.00</td>
                  <td class="cost_data_1">7.80</td>
                  <td class="rev_data_1">110.97</td>

                  <td class="pnl_data">78.07</td>

                  <td class="unreg_data">417.00</td>

                  <td class="month_end_spending_data">23.40</td>

                  <td class="waki_gross_rev_data">2.34</td>

                  <td class="month_end_data">0.70</td>

                  <td class="comparison_mo_data">-100.00</td>
                  <td class="comparison_cost_data">-100.00</td>
                  <td class="comparison_rev_data">-823.93</td>
                  <td class="weekly_caps_data">0</td>
                  <td class="percent_caps_data">0</td>


                  <td class="roiprice_mo_data">3.90</td>
                  <td class="monthly_arpu_data">237.7834</td>
                  <td class="roi_data_1">0.02</td>
                  <td class="subs_data">2.00</td>
                  <td class="average_daily_rev_data">146.46</td>
                  <td class="average_daily_cost_data">2.23</td>
                  <td class="average_daily_pnl_data">65.72</td>
                  <td class="average_daily_mo_data">0.57</td>
                  <td class="average_daily_unreg_data">59.57</td>
                </tr>
                <tr class="roiRow">
                  <td>MM - Mm-mytel-linkit</td>

                  <td class="mo_data_0">319.00</td>
                  <td class="cost_data_0">55.12</td>
                  <td class="rev_data_0">739.81</td>
                  <td class="mo_data_1">35.00</td>
                  <td class="cost_data_1">6.24</td>
                  <td class="rev_data_1">103.48</td>

                  <td class="pnl_data">78.62</td>

                  <td class="unreg_data">719.00</td>

                  <td class="month_end_spending_data">61.36</td>

                  <td class="waki_gross_rev_data">1.87</td>

                  <td class="month_end_data">0.56</td>

                  <td class="comparison_mo_data">-811.43</td>
                  <td class="comparison_cost_data">-783.33</td>
                  <td class="comparison_rev_data">-614.91</td>
                  <td class="weekly_caps_data">0</td>
                  <td class="percent_caps_data">0</td>


                  <td class="roiprice_mo_data">0.18</td>
                  <td class="monthly_arpu_data">12.6714</td>
                  <td class="roi_data_1">0.01</td>
                  <td class="subs_data">35.00</td>
                  <td class="average_daily_rev_data">105.69</td>
                  <td class="average_daily_cost_data">7.87</td>
                  <td class="average_daily_pnl_data">13.42</td>
                  <td class="average_daily_mo_data">45.57</td>
                  <td class="average_daily_unreg_data">102.71</td>
                </tr>
                <tr class="roiRow">
                  <td>ID - Id-isat-waki</td>

                  <td class="mo_data_0">0.00</td>
                  <td class="cost_data_0">0.00</td>
                  <td class="rev_data_0">667.15</td>
                  <td class="mo_data_1">0.00</td>
                  <td class="cost_data_1">0.00</td>
                  <td class="rev_data_1">100.38</td>

                  <td class="pnl_data">74.79</td>

                  <td class="unreg_data">93.00</td>

                  <td class="month_end_spending_data">0.00</td>

                  <td class="waki_gross_rev_data">0.00</td>

                  <td class="month_end_data">0.00</td>

                  <td class="comparison_mo_data">100.00</td>
                  <td class="comparison_cost_data">100.00</td>
                  <td class="comparison_rev_data">-564.59</td>
                  <td class="weekly_caps_data">0</td>
                  <td class="percent_caps_data">0</td>


                  <td class="roiprice_mo_data">0</td>
                  <td class="monthly_arpu_data">0</td>
                  <td class="roi_data_1">0</td>
                  <td class="subs_data">0</td>
                  <td class="average_daily_rev_data">95.31</td>
                  <td class="average_daily_cost_data">0</td>
                  <td class="average_daily_pnl_data">0</td>
                  <td class="average_daily_mo_data">0</td>
                  <td class="average_daily_unreg_data">13.29</td>
                </tr>
                <tr class="roiRow">
                  <td>ID - Indosat</td>

                  <td class="mo_data_0">0.00</td>
                  <td class="cost_data_0">0.00</td>
                  <td class="rev_data_0">880.98</td>
                  <td class="mo_data_1">0.00</td>
                  <td class="cost_data_1">0.00</td>
                  <td class="rev_data_1">89.62</td>

                  <td class="pnl_data">73.49</td>

                  <td class="unreg_data">513.00</td>

                  <td class="month_end_spending_data">0.00</td>

                  <td class="waki_gross_rev_data">0.00</td>

                  <td class="month_end_data">0.00</td>

                  <td class="comparison_mo_data">100.00</td>
                  <td class="comparison_cost_data">100.00</td>
                  <td class="comparison_rev_data">-883.00</td>
                  <td class="weekly_caps_data">0</td>
                  <td class="percent_caps_data">0</td>


                  <td class="roiprice_mo_data">0</td>
                  <td class="monthly_arpu_data">0</td>
                  <td class="roi_data_1">0</td>
                  <td class="subs_data">0</td>
                  <td class="average_daily_rev_data">125.85</td>
                  <td class="average_daily_cost_data">0</td>
                  <td class="average_daily_pnl_data">0</td>
                  <td class="average_daily_mo_data">0</td>
                  <td class="average_daily_unreg_data">73.29</td>
                </tr>
                <tr class="roiRow">
                  <td>TH - Th-ais-cm</td>

                  <td class="mo_data_0">0.00</td>
                  <td class="cost_data_0">0.00</td>
                  <td class="rev_data_0">612.35</td>
                  <td class="mo_data_1">0.00</td>
                  <td class="cost_data_1">0.00</td>
                  <td class="rev_data_1">86.38</td>

                  <td class="pnl_data">70.83</td>

                  <td class="unreg_data">0.00</td>

                  <td class="month_end_spending_data">0.00</td>

                  <td class="waki_gross_rev_data">0.00</td>

                  <td class="month_end_data">0.00</td>

                  <td class="comparison_mo_data">100.00</td>
                  <td class="comparison_cost_data">100.00</td>
                  <td class="comparison_rev_data">-608.89</td>
                  <td class="weekly_caps_data">0</td>
                  <td class="percent_caps_data">0</td>


                  <td class="roiprice_mo_data">0</td>
                  <td class="monthly_arpu_data">0</td>
                  <td class="roi_data_1">0</td>
                  <td class="subs_data">0</td>
                  <td class="average_daily_rev_data">87.48</td>
                  <td class="average_daily_cost_data">0</td>
                  <td class="average_daily_pnl_data">0</td>
                  <td class="average_daily_mo_data">0</td>
                  <td class="average_daily_unreg_data">0</td>
                </tr>
                <tr class="roiRow">
                  <td>ID - Id-xl-waki</td>

                  <td class="mo_data_0">0.00</td>
                  <td class="cost_data_0">0.00</td>
                  <td class="rev_data_0">513.40</td>
                  <td class="mo_data_1">0.00</td>
                  <td class="cost_data_1">0.00</td>
                  <td class="rev_data_1">70.56</td>

                  <td class="pnl_data">52.57</td>

                  <td class="unreg_data">3.00</td>

                  <td class="month_end_spending_data">0.00</td>

                  <td class="waki_gross_rev_data">0.00</td>

                  <td class="month_end_data">0.00</td>

                  <td class="comparison_mo_data">100.00</td>
                  <td class="comparison_cost_data">100.00</td>
                  <td class="comparison_rev_data">-627.56</td>
                  <td class="weekly_caps_data">0</td>
                  <td class="percent_caps_data">0</td>


                  <td class="roiprice_mo_data">0</td>
                  <td class="monthly_arpu_data">0</td>
                  <td class="roi_data_1">0</td>
                  <td class="subs_data">0</td>
                  <td class="average_daily_rev_data">73.34</td>
                  <td class="average_daily_cost_data">0</td>
                  <td class="average_daily_pnl_data">0</td>
                  <td class="average_daily_mo_data">0</td>
                  <td class="average_daily_unreg_data">0.43</td>
                </tr>
                <tr class="roiRow">
                  <td>TH - Th-ais-gmob</td>

                  <td class="mo_data_0">4,584.00</td>
                  <td class="cost_data_0">279.24</td>
                  <td class="rev_data_0">719.89</td>
                  <td class="mo_data_1">31.00</td>
                  <td class="cost_data_1">2.34</td>
                  <td class="rev_data_1">70.44</td>

                  <td class="pnl_data">55.42</td>

                  <td class="unreg_data">2,504.00</td>

                  <td class="month_end_spending_data">281.58</td>

                  <td class="waki_gross_rev_data">0.70</td>

                  <td class="month_end_data">0.21</td>

                  <td class="comparison_mo_data">-14,687.10</td>
                  <td class="comparison_cost_data">-11,833.33</td>
                  <td class="comparison_rev_data">-921.99</td>
                  <td class="weekly_caps_data">0</td>
                  <td class="percent_caps_data">0</td>


                  <td class="roiprice_mo_data">0.08</td>
                  <td class="monthly_arpu_data">9.7383</td>
                  <td class="roi_data_1">0.01</td>
                  <td class="subs_data">31.00</td>
                  <td class="average_daily_rev_data">102.84</td>
                  <td class="average_daily_cost_data">39.89</td>
                  <td class="average_daily_pnl_data">2.58</td>
                  <td class="average_daily_mo_data">654.86</td>
                  <td class="average_daily_unreg_data">357.71</td>
                </tr>
                <tr class="roiRow">
                  <td>ID - Id-isat-pass</td>

                  <td class="mo_data_0">0.00</td>
                  <td class="cost_data_0">0.00</td>
                  <td class="rev_data_0">493.46</td>
                  <td class="mo_data_1">0.00</td>
                  <td class="cost_data_1">0.00</td>
                  <td class="rev_data_1">68.40</td>

                  <td class="pnl_data">50.96</td>

                  <td class="unreg_data">40.00</td>

                  <td class="month_end_spending_data">0.00</td>

                  <td class="waki_gross_rev_data">0.00</td>

                  <td class="month_end_data">0.00</td>

                  <td class="comparison_mo_data">100.00</td>
                  <td class="comparison_cost_data">100.00</td>
                  <td class="comparison_rev_data">-621.45</td>
                  <td class="weekly_caps_data">0</td>
                  <td class="percent_caps_data">0</td>


                  <td class="roiprice_mo_data">0</td>
                  <td class="monthly_arpu_data">0</td>
                  <td class="roi_data_1">0</td>
                  <td class="subs_data">0</td>
                  <td class="average_daily_rev_data">70.49</td>
                  <td class="average_daily_cost_data">0</td>
                  <td class="average_daily_pnl_data">0</td>
                  <td class="average_daily_mo_data">0</td>
                  <td class="average_daily_unreg_data">5.71</td>
                </tr>
                <tr class="roiRow">
                  <td>ID - Id-xl-pass</td>

                  <td class="mo_data_0">0.00</td>
                  <td class="cost_data_0">0.00</td>
                  <td class="rev_data_0">462.44</td>
                  <td class="mo_data_1">0.00</td>
                  <td class="cost_data_1">0.00</td>
                  <td class="rev_data_1">63.49</td>

                  <td class="pnl_data">47.30</td>

                  <td class="unreg_data">8.00</td>

                  <td class="month_end_spending_data">0.00</td>

                  <td class="waki_gross_rev_data">0.00</td>

                  <td class="month_end_data">0.00</td>

                  <td class="comparison_mo_data">100.00</td>
                  <td class="comparison_cost_data">100.00</td>
                  <td class="comparison_rev_data">-628.34</td>
                  <td class="weekly_caps_data">0</td>
                  <td class="percent_caps_data">0</td>


                  <td class="roiprice_mo_data">0</td>
                  <td class="monthly_arpu_data">0</td>
                  <td class="roi_data_1">0</td>
                  <td class="subs_data">0</td>
                  <td class="average_daily_rev_data">66.06</td>
                  <td class="average_daily_cost_data">0</td>
                  <td class="average_daily_pnl_data">0</td>
                  <td class="average_daily_mo_data">0</td>
                  <td class="average_daily_unreg_data">1.14</td>
                </tr>
                <tr class="roiRow">
                  <td>SE - Se-all-linkit</td>

                  <td class="mo_data_0">179.00</td>
                  <td class="cost_data_0">915.60</td>
                  <td class="rev_data_0">2,061.63</td>
                  <td class="mo_data_1">36.00</td>
                  <td class="cost_data_1">159.60</td>
                  <td class="rev_data_1">58.07</td>

                  <td class="pnl_data">-111.98</td>

                  <td class="unreg_data">84.00</td>

                  <td class="month_end_spending_data">1,075.20</td>

                  <td class="waki_gross_rev_data">47.88</td>

                  <td class="month_end_data">14.36</td>

                  <td class="comparison_mo_data">-397.22</td>
                  <td class="comparison_cost_data">-473.68</td>
                  <td class="comparison_rev_data">-3,450.00</td>
                  <td class="weekly_caps_data">0</td>
                  <td class="percent_caps_data">0</td>


                  <td class="roiprice_mo_data">4.43</td>
                  <td class="monthly_arpu_data">6.9136</td>
                  <td class="roi_data_1">0.64</td>
                  <td class="subs_data">36.00</td>
                  <td class="average_daily_rev_data">294.52</td>
                  <td class="average_daily_cost_data">130.80</td>
                  <td class="average_daily_pnl_data">2.25</td>
                  <td class="average_daily_mo_data">25.57</td>
                  <td class="average_daily_unreg_data">12.00</td>
                </tr>
                <tr class="roiRow">
                  <td>TH - Th-ais-qr</td>

                  <td class="mo_data_0">1,656.00</td>
                  <td class="cost_data_0">189.54</td>
                  <td class="rev_data_0">913.19</td>
                  <td class="mo_data_1">137.00</td>
                  <td class="cost_data_1">17.29</td>
                  <td class="rev_data_1">49.37</td>

                  <td class="pnl_data">23.20</td>

                  <td class="unreg_data">1,803.00</td>

                  <td class="month_end_spending_data">206.83</td>

                  <td class="waki_gross_rev_data">5.19</td>

                  <td class="month_end_data">1.56</td>

                  <td class="comparison_mo_data">-1,108.76</td>
                  <td class="comparison_cost_data">-996.24</td>
                  <td class="comparison_rev_data">-1,749.49</td>
                  <td class="weekly_caps_data">0</td>
                  <td class="percent_caps_data">0</td>


                  <td class="roiprice_mo_data">0.13</td>
                  <td class="monthly_arpu_data">1.5446</td>
                  <td class="roi_data_1">0.08</td>
                  <td class="subs_data">137.00</td>
                  <td class="average_daily_rev_data">130.46</td>
                  <td class="average_daily_cost_data">27.08</td>
                  <td class="average_daily_pnl_data">4.82</td>
                  <td class="average_daily_mo_data">236.57</td>
                  <td class="average_daily_unreg_data">257.57</td>
                </tr>
                <tr class="roiRow">
                  <td>TL - Tcel</td>

                  <td class="mo_data_0">1,806.00</td>
                  <td class="cost_data_0">78.39</td>
                  <td class="rev_data_0">296.87</td>
                  <td class="mo_data_1">0.00</td>
                  <td class="cost_data_1">0.00</td>
                  <td class="rev_data_1">42.21</td>

                  <td class="pnl_data">30.39</td>

                  <td class="unreg_data">13.00</td>

                  <td class="month_end_spending_data">78.39</td>

                  <td class="waki_gross_rev_data">0.00</td>

                  <td class="month_end_data">0.00</td>

                  <td class="comparison_mo_data">100.00</td>
                  <td class="comparison_cost_data">100.00</td>
                  <td class="comparison_rev_data">-603.30</td>
                  <td class="weekly_caps_data">0</td>
                  <td class="percent_caps_data">0</td>


                  <td class="roiprice_mo_data">0</td>
                  <td class="monthly_arpu_data">0</td>
                  <td class="roi_data_1">0</td>
                  <td class="subs_data">0</td>
                  <td class="average_daily_rev_data">42.41</td>
                  <td class="average_daily_cost_data">11.20</td>
                  <td class="average_daily_pnl_data">3.79</td>
                  <td class="average_daily_mo_data">258.00</td>
                  <td class="average_daily_unreg_data">1.86</td>
                </tr>
                <tr class="roiRow">
                  <td>LA - Unitel</td>

                  <td class="mo_data_0">488.00</td>
                  <td class="cost_data_0">105.04</td>
                  <td class="rev_data_0">287.68</td>
                  <td class="mo_data_1">29.00</td>
                  <td class="cost_data_1">5.72</td>
                  <td class="rev_data_1">40.72</td>

                  <td class="pnl_data">29.71</td>

                  <td class="unreg_data">94.00</td>

                  <td class="month_end_spending_data">110.76</td>

                  <td class="waki_gross_rev_data">1.72</td>

                  <td class="month_end_data">0.51</td>

                  <td class="comparison_mo_data">-1,582.76</td>
                  <td class="comparison_cost_data">-1,736.36</td>
                  <td class="comparison_rev_data">-606.46</td>
                  <td class="weekly_caps_data">0</td>
                  <td class="percent_caps_data">0</td>


                  <td class="roiprice_mo_data">0.20</td>
                  <td class="monthly_arpu_data">6.0180</td>
                  <td class="roi_data_1">0.03</td>
                  <td class="subs_data">29.00</td>
                  <td class="average_daily_rev_data">41.10</td>
                  <td class="average_daily_cost_data">15.01</td>
                  <td class="average_daily_pnl_data">2.74</td>
                  <td class="average_daily_mo_data">69.71</td>
                  <td class="average_daily_unreg_data">13.43</td>
                </tr>
                <tr class="roiRow">
                  <td>ID - Id-smartfren-yatta</td>

                  <td class="mo_data_0">2,936.00</td>
                  <td class="cost_data_0">28.49</td>
                  <td class="rev_data_0">341.47</td>
                  <td class="mo_data_1">532.00</td>
                  <td class="cost_data_1">5.07</td>
                  <td class="rev_data_1">39.91</td>

                  <td class="pnl_data">27.66</td>

                  <td class="unreg_data">21,114.00</td>

                  <td class="month_end_spending_data">33.56</td>

                  <td class="waki_gross_rev_data">1.52</td>

                  <td class="month_end_data">0.46</td>

                  <td class="comparison_mo_data">-451.88</td>
                  <td class="comparison_cost_data">-461.93</td>
                  <td class="comparison_rev_data">-755.61</td>
                  <td class="weekly_caps_data">0</td>
                  <td class="percent_caps_data">0</td>


                  <td class="roiprice_mo_data">0.01</td>
                  <td class="monthly_arpu_data">0.3215</td>
                  <td class="roi_data_1">0.03</td>
                  <td class="subs_data">532.00</td>
                  <td class="average_daily_rev_data">48.78</td>
                  <td class="average_daily_cost_data">4.07</td>
                  <td class="average_daily_pnl_data">11.99</td>
                  <td class="average_daily_mo_data">419.43</td>
                  <td class="average_daily_unreg_data">3,016.29</td>
                </tr>
                <tr class="roiRow">
                  <td>TH - Th-ais-gmob-r01-r03</td>

                  <td class="mo_data_0">3.00</td>
                  <td class="cost_data_0">0.00</td>
                  <td class="rev_data_0">304.76</td>
                  <td class="mo_data_1">35.00</td>
                  <td class="cost_data_1">2.34</td>
                  <td class="rev_data_1">33.55</td>

                  <td class="pnl_data">21.82</td>

                  <td class="unreg_data">940.00</td>

                  <td class="month_end_spending_data">2.34</td>

                  <td class="waki_gross_rev_data">0.70</td>

                  <td class="month_end_data">0.21</td>

                  <td class="comparison_mo_data">91.43</td>
                  <td class="comparison_cost_data">100.00</td>
                  <td class="comparison_rev_data">-808.36</td>
                  <td class="weekly_caps_data">0</td>
                  <td class="percent_caps_data">0</td>


                  <td class="roiprice_mo_data">0.07</td>
                  <td class="monthly_arpu_data">4.1083</td>
                  <td class="roi_data_1">0.02</td>
                  <td class="subs_data">35.00</td>
                  <td class="average_daily_rev_data">43.54</td>
                  <td class="average_daily_cost_data">0</td>
                  <td class="average_daily_pnl_data">0</td>
                  <td class="average_daily_mo_data">0.43</td>
                  <td class="average_daily_unreg_data">134.29</td>
                </tr>
                <tr class="roiRow">
                  <td>TH - Th-ais-mks</td>

                  <td class="mo_data_0">6,510.00</td>
                  <td class="cost_data_0">499.20</td>
                  <td class="rev_data_0">320.62</td>
                  <td class="mo_data_1">662.00</td>
                  <td class="cost_data_1">53.82</td>
                  <td class="rev_data_1">32.45</td>

                  <td class="pnl_data">-30.19</td>

                  <td class="unreg_data">5,821.00</td>

                  <td class="month_end_spending_data">553.02</td>

                  <td class="waki_gross_rev_data">16.15</td>

                  <td class="month_end_data">4.84</td>

                  <td class="comparison_mo_data">-883.38</td>
                  <td class="comparison_cost_data">-827.54</td>
                  <td class="comparison_rev_data">-887.94</td>
                  <td class="weekly_caps_data">0</td>
                  <td class="percent_caps_data">0</td>


                  <td class="roiprice_mo_data">0.08</td>
                  <td class="monthly_arpu_data">0.2101</td>
                  <td class="roi_data_1">0.39</td>
                  <td class="subs_data">662.00</td>
                  <td class="average_daily_rev_data">45.80</td>
                  <td class="average_daily_cost_data">71.31</td>
                  <td class="average_daily_pnl_data">0.64</td>
                  <td class="average_daily_mo_data">930.00</td>
                  <td class="average_daily_unreg_data">831.57</td>
                </tr>
                <tr class="roiRow">
                  <td>PK - Pk-telenor-linkit</td>

                  <td class="mo_data_0">24,539.00</td>
                  <td class="cost_data_0">638.30</td>
                  <td class="rev_data_0">250.39</td>
                  <td class="mo_data_1">3,502.00</td>
                  <td class="cost_data_1">90.74</td>
                  <td class="rev_data_1">32.19</td>

                  <td class="pnl_data">-67.56</td>

                  <td class="unreg_data">82.00</td>

                  <td class="month_end_spending_data">729.04</td>

                  <td class="waki_gross_rev_data">27.22</td>

                  <td class="month_end_data">8.17</td>

                  <td class="comparison_mo_data">-600.71</td>
                  <td class="comparison_cost_data">-603.44</td>
                  <td class="comparison_rev_data">-677.82</td>
                  <td class="weekly_caps_data">0</td>
                  <td class="percent_caps_data">0</td>


                  <td class="roiprice_mo_data">0.03</td>
                  <td class="monthly_arpu_data">0.0394</td>
                  <td class="roi_data_1">0.66</td>
                  <td class="subs_data">3,502.00</td>
                  <td class="average_daily_rev_data">35.77</td>
                  <td class="average_daily_cost_data">91.19</td>
                  <td class="average_daily_pnl_data">0.39</td>
                  <td class="average_daily_mo_data">3,505.57</td>
                  <td class="average_daily_unreg_data">11.71</td>
                </tr>
                <tr class="roiRow">
                  <td>PL - Pol-plus-linkit</td>

                  <td class="mo_data_0">138.00</td>
                  <td class="cost_data_0">351.00</td>
                  <td class="rev_data_0">814.31</td>
                  <td class="mo_data_1">19.00</td>
                  <td class="cost_data_1">40.95</td>
                  <td class="rev_data_1">29.18</td>

                  <td class="pnl_data">-19.94</td>

                  <td class="unreg_data">0.00</td>

                  <td class="month_end_spending_data">391.95</td>

                  <td class="waki_gross_rev_data">12.29</td>

                  <td class="month_end_data">3.69</td>

                  <td class="comparison_mo_data">-626.32</td>
                  <td class="comparison_cost_data">-757.14</td>
                  <td class="comparison_rev_data">-2,690.91</td>
                  <td class="weekly_caps_data">0</td>
                  <td class="percent_caps_data">0</td>


                  <td class="roiprice_mo_data">2.16</td>
                  <td class="monthly_arpu_data">6.5813</td>
                  <td class="roi_data_1">0.33</td>
                  <td class="subs_data">19.00</td>
                  <td class="average_daily_rev_data">116.33</td>
                  <td class="average_daily_cost_data">50.14</td>
                  <td class="average_daily_pnl_data">2.32</td>
                  <td class="average_daily_mo_data">19.71</td>
                  <td class="average_daily_unreg_data">0</td>
                </tr>
                <tr class="roiRow">
                  <td>PS - Pse-jawwal-kidzo</td>

                  <td class="mo_data_0">0.00</td>
                  <td class="cost_data_0">0.00</td>
                  <td class="rev_data_0">254.48</td>
                  <td class="mo_data_1">0.00</td>
                  <td class="cost_data_1">0.00</td>
                  <td class="rev_data_1">28.25</td>

                  <td class="pnl_data">20.34</td>

                  <td class="unreg_data">172.00</td>

                  <td class="month_end_spending_data">0.00</td>

                  <td class="waki_gross_rev_data">0.00</td>

                  <td class="month_end_data">0.00</td>

                  <td class="comparison_mo_data">100.00</td>
                  <td class="comparison_cost_data">100.00</td>
                  <td class="comparison_rev_data">-800.70</td>
                  <td class="weekly_caps_data">0</td>
                  <td class="percent_caps_data">0</td>


                  <td class="roiprice_mo_data">0</td>
                  <td class="monthly_arpu_data">0</td>
                  <td class="roi_data_1">0</td>
                  <td class="subs_data">0</td>
                  <td class="average_daily_rev_data">36.35</td>
                  <td class="average_daily_cost_data">0</td>
                  <td class="average_daily_pnl_data">0</td>
                  <td class="average_daily_mo_data">0</td>
                  <td class="average_daily_unreg_data">24.57</td>
                </tr>
                <tr class="roiRow">
                  <td>KH - Metfone-pax</td>

                  <td class="mo_data_0">0.00</td>
                  <td class="cost_data_0">0.00</td>
                  <td class="rev_data_0">214.10</td>
                  <td class="mo_data_1">0.00</td>
                  <td class="cost_data_1">0.00</td>
                  <td class="rev_data_1">27.93</td>

                  <td class="pnl_data">12.72</td>

                  <td class="unreg_data">226.00</td>

                  <td class="month_end_spending_data">0.00</td>

                  <td class="waki_gross_rev_data">0.00</td>

                  <td class="month_end_data">0.00</td>

                  <td class="comparison_mo_data">100.00</td>
                  <td class="comparison_cost_data">100.00</td>
                  <td class="comparison_rev_data">-666.70</td>
                  <td class="weekly_caps_data">0</td>
                  <td class="percent_caps_data">0</td>


                  <td class="roiprice_mo_data">0</td>
                  <td class="monthly_arpu_data">0</td>
                  <td class="roi_data_1">0</td>
                  <td class="subs_data">0</td>
                  <td class="average_daily_rev_data">30.59</td>
                  <td class="average_daily_cost_data">0</td>
                  <td class="average_daily_pnl_data">0</td>
                  <td class="average_daily_mo_data">0</td>
                  <td class="average_daily_unreg_data">32.29</td>
                </tr>
                <tr class="roiRow">
                  <td>VN - Vinaphone</td>

                  <td class="mo_data_0">0.00</td>
                  <td class="cost_data_0">0.00</td>
                  <td class="rev_data_0">474.76</td>
                  <td class="mo_data_1">0.00</td>
                  <td class="cost_data_1">0.00</td>
                  <td class="rev_data_1">27.28</td>

                  <td class="pnl_data">22.37</td>

                  <td class="unreg_data">123.00</td>

                  <td class="month_end_spending_data">0.00</td>

                  <td class="waki_gross_rev_data">0.00</td>

                  <td class="month_end_data">0.00</td>

                  <td class="comparison_mo_data">100.00</td>
                  <td class="comparison_cost_data">100.00</td>
                  <td class="comparison_rev_data">-1,640.56</td>
                  <td class="weekly_caps_data">0</td>
                  <td class="percent_caps_data">0</td>


                  <td class="roiprice_mo_data">0</td>
                  <td class="monthly_arpu_data">0</td>
                  <td class="roi_data_1">0</td>
                  <td class="subs_data">0</td>
                  <td class="average_daily_rev_data">67.82</td>
                  <td class="average_daily_cost_data">0</td>
                  <td class="average_daily_pnl_data">0</td>
                  <td class="average_daily_mo_data">0</td>
                  <td class="average_daily_unreg_data">17.57</td>
                </tr>
                <tr class="roiRow">
                  <td>OM - Omn-ooredoo-linkit</td>

                  <td class="mo_data_0">186.00</td>
                  <td class="cost_data_0">302.61</td>
                  <td class="rev_data_0">302.05</td>
                  <td class="mo_data_1">0.00</td>
                  <td class="cost_data_1">0.00</td>
                  <td class="rev_data_1">25.76</td>

                  <td class="pnl_data">18.55</td>

                  <td class="unreg_data">541.00</td>

                  <td class="month_end_spending_data">302.61</td>

                  <td class="waki_gross_rev_data">0.00</td>

                  <td class="month_end_data">0.00</td>

                  <td class="comparison_mo_data">100.00</td>
                  <td class="comparison_cost_data">100.00</td>
                  <td class="comparison_rev_data">-1,072.53</td>
                  <td class="weekly_caps_data">0</td>
                  <td class="percent_caps_data">0</td>


                  <td class="roiprice_mo_data">0</td>
                  <td class="monthly_arpu_data">0</td>
                  <td class="roi_data_1">0</td>
                  <td class="subs_data">0</td>
                  <td class="average_daily_rev_data">43.15</td>
                  <td class="average_daily_cost_data">43.23</td>
                  <td class="average_daily_pnl_data">1.00</td>
                  <td class="average_daily_mo_data">26.57</td>
                  <td class="average_daily_unreg_data">77.29</td>
                </tr>
                <tr class="roiRow">
                  <td>RS - Srb-nth-linkit</td>

                  <td class="mo_data_0">0.00</td>
                  <td class="cost_data_0">0.00</td>
                  <td class="rev_data_0">48.64</td>
                  <td class="mo_data_1">0.00</td>
                  <td class="cost_data_1">0.00</td>
                  <td class="rev_data_1">22.17</td>

                  <td class="pnl_data">18.18</td>

                  <td class="unreg_data">0.00</td>

                  <td class="month_end_spending_data">0.00</td>

                  <td class="waki_gross_rev_data">0.00</td>

                  <td class="month_end_data">0.00</td>

                  <td class="comparison_mo_data">100.00</td>
                  <td class="comparison_cost_data">100.00</td>
                  <td class="comparison_rev_data">-119.35</td>
                  <td class="weekly_caps_data">0</td>
                  <td class="percent_caps_data">0</td>


                  <td class="roiprice_mo_data">0</td>
                  <td class="monthly_arpu_data">0</td>
                  <td class="roi_data_1">0</td>
                  <td class="subs_data">0</td>
                  <td class="average_daily_rev_data">6.95</td>
                  <td class="average_daily_cost_data">0</td>
                  <td class="average_daily_pnl_data">0</td>
                  <td class="average_daily_mo_data">0</td>
                  <td class="average_daily_unreg_data">0</td>
                </tr>
                <tr class="roiRow">
                  <td>ZA - Za-mtn-mondia</td>

                  <td class="mo_data_0">0.00</td>
                  <td class="cost_data_0">0.00</td>
                  <td class="rev_data_0">410.18</td>
                  <td class="mo_data_1">0.00</td>
                  <td class="cost_data_1">0.00</td>
                  <td class="rev_data_1">20.81</td>

                  <td class="pnl_data">14.98</td>

                  <td class="unreg_data">0.00</td>

                  <td class="month_end_spending_data">0.00</td>

                  <td class="waki_gross_rev_data">0.00</td>

                  <td class="month_end_data">0.00</td>

                  <td class="comparison_mo_data">100.00</td>
                  <td class="comparison_cost_data">100.00</td>
                  <td class="comparison_rev_data">-1,871.01</td>
                  <td class="weekly_caps_data">0</td>
                  <td class="percent_caps_data">0</td>


                  <td class="roiprice_mo_data">0</td>
                  <td class="monthly_arpu_data">0</td>
                  <td class="roi_data_1">0</td>
                  <td class="subs_data">0</td>
                  <td class="average_daily_rev_data">58.60</td>
                  <td class="average_daily_cost_data">0</td>
                  <td class="average_daily_pnl_data">0</td>
                  <td class="average_daily_mo_data">0</td>
                  <td class="average_daily_unreg_data">0</td>
                </tr>
                <tr class="roiRow">
                  <td>ID - Id-smartfren-waki</td>

                  <td class="mo_data_0">28.00</td>
                  <td class="cost_data_0">0.26</td>
                  <td class="rev_data_0">176.94</td>
                  <td class="mo_data_1">6.00</td>
                  <td class="cost_data_1">0.00</td>
                  <td class="rev_data_1">20.56</td>

                  <td class="pnl_data">14.81</td>

                  <td class="unreg_data">3,718.00</td>

                  <td class="month_end_spending_data">0.26</td>

                  <td class="waki_gross_rev_data">0.00</td>

                  <td class="month_end_data">0.00</td>

                  <td class="comparison_mo_data">-366.67</td>
                  <td class="comparison_cost_data">100.00</td>
                  <td class="comparison_rev_data">-760.48</td>
                  <td class="weekly_caps_data">0</td>
                  <td class="percent_caps_data">0</td>


                  <td class="roiprice_mo_data">0</td>
                  <td class="monthly_arpu_data">14.6876</td>
                  <td class="roi_data_1">0</td>
                  <td class="subs_data">6.00</td>
                  <td class="average_daily_rev_data">25.28</td>
                  <td class="average_daily_cost_data">0.04</td>
                  <td class="average_daily_pnl_data">680.53</td>
                  <td class="average_daily_mo_data">4.00</td>
                  <td class="average_daily_unreg_data">531.14</td>
                </tr>
                <tr class="roiRow">
                  <td>NG - Nga-mtn-finklasic</td>

                  <td class="mo_data_0">1.00</td>
                  <td class="cost_data_0">0.26</td>
                  <td class="rev_data_0">94.60</td>
                  <td class="mo_data_1">0.00</td>
                  <td class="cost_data_1">0.00</td>
                  <td class="rev_data_1">19.44</td>

                  <td class="pnl_data">13.99</td>

                  <td class="unreg_data">45.00</td>

                  <td class="month_end_spending_data">0.26</td>

                  <td class="waki_gross_rev_data">0.00</td>

                  <td class="month_end_data">0.00</td>

                  <td class="comparison_mo_data">100.00</td>
                  <td class="comparison_cost_data">100.00</td>
                  <td class="comparison_rev_data">-386.70</td>
                  <td class="weekly_caps_data">0</td>
                  <td class="percent_caps_data">0</td>


                  <td class="roiprice_mo_data">0</td>
                  <td class="monthly_arpu_data">0</td>
                  <td class="roi_data_1">0</td>
                  <td class="subs_data">0</td>
                  <td class="average_daily_rev_data">13.51</td>
                  <td class="average_daily_cost_data">0.04</td>
                  <td class="average_daily_pnl_data">363.84</td>
                  <td class="average_daily_mo_data">0.14</td>
                  <td class="average_daily_unreg_data">6.43</td>
                </tr>
                <tr class="roiRow">
                  <td>TH - Dtac</td>

                  <td class="mo_data_0">1.00</td>
                  <td class="cost_data_0">0.00</td>
                  <td class="rev_data_0">124.04</td>
                  <td class="mo_data_1">0.00</td>
                  <td class="cost_data_1">0.00</td>
                  <td class="rev_data_1">17.06</td>

                  <td class="pnl_data">13.99</td>

                  <td class="unreg_data">1,685.00</td>

                  <td class="month_end_spending_data">0.00</td>

                  <td class="waki_gross_rev_data">0.00</td>

                  <td class="month_end_data">0.00</td>

                  <td class="comparison_mo_data">100.00</td>
                  <td class="comparison_cost_data">100.00</td>
                  <td class="comparison_rev_data">-627.11</td>
                  <td class="weekly_caps_data">0</td>
                  <td class="percent_caps_data">0</td>


                  <td class="roiprice_mo_data">0</td>
                  <td class="monthly_arpu_data">0</td>
                  <td class="roi_data_1">0</td>
                  <td class="subs_data">0</td>
                  <td class="average_daily_rev_data">17.72</td>
                  <td class="average_daily_cost_data">0</td>
                  <td class="average_daily_pnl_data">0</td>
                  <td class="average_daily_mo_data">0.14</td>
                  <td class="average_daily_unreg_data">240.71</td>
                </tr>
                <tr class="roiRow">
                  <td>PS - Pse-jawwal-linkit</td>

                  <td class="mo_data_0">825.00</td>
                  <td class="cost_data_0">263.94</td>
                  <td class="rev_data_0">103.97</td>
                  <td class="mo_data_1">71.00</td>
                  <td class="cost_data_1">23.20</td>
                  <td class="rev_data_1">13.35</td>

                  <td class="pnl_data">-13.59</td>

                  <td class="unreg_data">18.00</td>

                  <td class="month_end_spending_data">287.14</td>

                  <td class="waki_gross_rev_data">6.96</td>

                  <td class="month_end_data">2.09</td>

                  <td class="comparison_mo_data">-1,061.97</td>
                  <td class="comparison_cost_data">-1,037.67</td>
                  <td class="comparison_rev_data">-678.95</td>
                  <td class="weekly_caps_data">0</td>
                  <td class="percent_caps_data">0</td>


                  <td class="roiprice_mo_data">0.33</td>
                  <td class="monthly_arpu_data">0.8057</td>
                  <td class="roi_data_1">0.41</td>
                  <td class="subs_data">71.00</td>
                  <td class="average_daily_rev_data">14.85</td>
                  <td class="average_daily_cost_data">37.71</td>
                  <td class="average_daily_pnl_data">0.39</td>
                  <td class="average_daily_mo_data">117.86</td>
                  <td class="average_daily_unreg_data">2.57</td>
                </tr>
                <tr class="roiRow">
                  <td>PH - Ph-smart-zed</td>

                  <td class="mo_data_0">0.00</td>
                  <td class="cost_data_0">0.00</td>
                  <td class="rev_data_0">78.31</td>
                  <td class="mo_data_1">0.00</td>
                  <td class="cost_data_1">0.00</td>
                  <td class="rev_data_1">10.14</td>

                  <td class="pnl_data">7.30</td>

                  <td class="unreg_data">139.00</td>

                  <td class="month_end_spending_data">0.00</td>

                  <td class="waki_gross_rev_data">0.00</td>

                  <td class="month_end_data">0.00</td>

                  <td class="comparison_mo_data">100.00</td>
                  <td class="comparison_cost_data">100.00</td>
                  <td class="comparison_rev_data">-672.41</td>
                  <td class="weekly_caps_data">0</td>
                  <td class="percent_caps_data">0</td>


                  <td class="roiprice_mo_data">0</td>
                  <td class="monthly_arpu_data">0</td>
                  <td class="roi_data_1">0</td>
                  <td class="subs_data">0</td>
                  <td class="average_daily_rev_data">11.19</td>
                  <td class="average_daily_cost_data">0</td>
                  <td class="average_daily_pnl_data">0</td>
                  <td class="average_daily_mo_data">0</td>
                  <td class="average_daily_unreg_data">19.86</td>
                </tr>
                <tr class="roiRow">
                  <td>PL - Pol-t-mobile</td>

                  <td class="mo_data_0">27.00</td>
                  <td class="cost_data_0">81.90</td>
                  <td class="rev_data_0">144.56</td>
                  <td class="mo_data_1">4.00</td>
                  <td class="cost_data_1">11.70</td>
                  <td class="rev_data_1">9.28</td>

                  <td class="pnl_data">-5.02</td>

                  <td class="unreg_data">0.00</td>

                  <td class="month_end_spending_data">93.60</td>

                  <td class="waki_gross_rev_data">3.51</td>

                  <td class="month_end_data">1.05</td>

                  <td class="comparison_mo_data">-575.00</td>
                  <td class="comparison_cost_data">-600.00</td>
                  <td class="comparison_rev_data">-1,457.14</td>
                  <td class="weekly_caps_data">0</td>
                  <td class="percent_caps_data">0</td>


                  <td class="roiprice_mo_data">2.93</td>
                  <td class="monthly_arpu_data">9.9468</td>
                  <td class="roi_data_1">0.29</td>
                  <td class="subs_data">4.00</td>
                  <td class="average_daily_rev_data">20.65</td>
                  <td class="average_daily_cost_data">11.70</td>
                  <td class="average_daily_pnl_data">1.77</td>
                  <td class="average_daily_mo_data">3.86</td>
                  <td class="average_daily_unreg_data">0</td>
                </tr>
                <tr class="roiRow">
                  <td>EG - Eg-orange</td>

                  <td class="mo_data_0">0.00</td>
                  <td class="cost_data_0">0.00</td>
                  <td class="rev_data_0">158.95</td>
                  <td class="mo_data_1">0.00</td>
                  <td class="cost_data_1">0.00</td>
                  <td class="rev_data_1">8.09</td>

                  <td class="pnl_data">6.08</td>

                  <td class="unreg_data">124.00</td>

                  <td class="month_end_spending_data">0.00</td>

                  <td class="waki_gross_rev_data">0.00</td>

                  <td class="month_end_data">0.00</td>

                  <td class="comparison_mo_data">100.00</td>
                  <td class="comparison_cost_data">100.00</td>
                  <td class="comparison_rev_data">-1,864.61</td>
                  <td class="weekly_caps_data">0</td>
                  <td class="percent_caps_data">0</td>


                  <td class="roiprice_mo_data">0</td>
                  <td class="monthly_arpu_data">0</td>
                  <td class="roi_data_1">0</td>
                  <td class="subs_data">0</td>
                  <td class="average_daily_rev_data">22.71</td>
                  <td class="average_daily_cost_data">0</td>
                  <td class="average_daily_pnl_data">0</td>
                  <td class="average_daily_mo_data">0</td>
                  <td class="average_daily_unreg_data">17.71</td>
                </tr>
                <tr class="roiRow">
                  <td>TH - Th-true-gm</td>

                  <td class="mo_data_0">0.00</td>
                  <td class="cost_data_0">0.00</td>
                  <td class="rev_data_0">53.71</td>
                  <td class="mo_data_1">0.00</td>
                  <td class="cost_data_1">0.00</td>
                  <td class="rev_data_1">7.64</td>

                  <td class="pnl_data">5.50</td>

                  <td class="unreg_data">1.00</td>

                  <td class="month_end_spending_data">0.00</td>

                  <td class="waki_gross_rev_data">0.00</td>

                  <td class="month_end_data">0.00</td>

                  <td class="comparison_mo_data">100.00</td>
                  <td class="comparison_cost_data">100.00</td>
                  <td class="comparison_rev_data">-603.28</td>
                  <td class="weekly_caps_data">0</td>
                  <td class="percent_caps_data">0</td>


                  <td class="roiprice_mo_data">0</td>
                  <td class="monthly_arpu_data">0</td>
                  <td class="roi_data_1">0</td>
                  <td class="subs_data">0</td>
                  <td class="average_daily_rev_data">7.67</td>
                  <td class="average_daily_cost_data">0</td>
                  <td class="average_daily_pnl_data">0</td>
                  <td class="average_daily_mo_data">0</td>
                  <td class="average_daily_unreg_data">0.14</td>
                </tr>
                <tr class="roiRow">
                  <td>AE - Uae-etisalat-airpay</td>

                  <td class="mo_data_0">121.00</td>
                  <td class="cost_data_0">0.00</td>
                  <td class="rev_data_0">80.38</td>
                  <td class="mo_data_1">15.00</td>
                  <td class="cost_data_1">0.00</td>
                  <td class="rev_data_1">6.84</td>

                  <td class="pnl_data">4.92</td>

                  <td class="unreg_data">428.00</td>

                  <td class="month_end_spending_data">0.00</td>

                  <td class="waki_gross_rev_data">0.00</td>

                  <td class="month_end_data">0.00</td>

                  <td class="comparison_mo_data">-706.67</td>
                  <td class="comparison_cost_data">100.00</td>
                  <td class="comparison_rev_data">-1,075.47</td>
                  <td class="weekly_caps_data">0</td>
                  <td class="percent_caps_data">0</td>


                  <td class="roiprice_mo_data">0</td>
                  <td class="monthly_arpu_data">1.9536</td>
                  <td class="roi_data_1">0</td>
                  <td class="subs_data">15.00</td>
                  <td class="average_daily_rev_data">11.48</td>
                  <td class="average_daily_cost_data">0</td>
                  <td class="average_daily_pnl_data">0</td>
                  <td class="average_daily_mo_data">17.29</td>
                  <td class="average_daily_unreg_data">61.14</td>
                </tr>
                <tr class="roiRow">
                  <td>TH - Th-ais-gemezz</td>

                  <td class="mo_data_0">0.00</td>
                  <td class="cost_data_0">0.00</td>
                  <td class="rev_data_0">40.67</td>
                  <td class="mo_data_1">0.00</td>
                  <td class="cost_data_1">0.00</td>
                  <td class="rev_data_1">6.12</td>

                  <td class="pnl_data">4.41</td>

                  <td class="unreg_data">0.00</td>

                  <td class="month_end_spending_data">0.00</td>

                  <td class="waki_gross_rev_data">0.00</td>

                  <td class="month_end_data">0.00</td>

                  <td class="comparison_mo_data">100.00</td>
                  <td class="comparison_cost_data">100.00</td>
                  <td class="comparison_rev_data">-564.55</td>
                  <td class="weekly_caps_data">0</td>
                  <td class="percent_caps_data">0</td>


                  <td class="roiprice_mo_data">0</td>
                  <td class="monthly_arpu_data">0</td>
                  <td class="roi_data_1">0</td>
                  <td class="subs_data">0</td>
                  <td class="average_daily_rev_data">5.81</td>
                  <td class="average_daily_cost_data">0</td>
                  <td class="average_daily_pnl_data">0</td>
                  <td class="average_daily_mo_data">0</td>
                  <td class="average_daily_unreg_data">0</td>
                </tr>
                <tr class="roiRow">
                  <td>KSA - Ksa-mobily</td>

                  <td class="mo_data_0">701.00</td>
                  <td class="cost_data_0">410.28</td>
                  <td class="rev_data_0">110.90</td>
                  <td class="mo_data_1">88.00</td>
                  <td class="cost_data_1">51.09</td>
                  <td class="rev_data_1">5.98</td>

                  <td class="pnl_data">-46.48</td>

                  <td class="unreg_data">1,767.00</td>

                  <td class="month_end_spending_data">461.37</td>

                  <td class="waki_gross_rev_data">15.33</td>

                  <td class="month_end_data">4.60</td>

                  <td class="comparison_mo_data">-696.59</td>
                  <td class="comparison_cost_data">-703.05</td>
                  <td class="comparison_rev_data">-1,753.33</td>
                  <td class="weekly_caps_data">0</td>
                  <td class="percent_caps_data">0</td>


                  <td class="roiprice_mo_data">0.58</td>
                  <td class="monthly_arpu_data">0.2914</td>
                  <td class="roi_data_1">1.99</td>
                  <td class="subs_data">88.00</td>
                  <td class="average_daily_rev_data">15.84</td>
                  <td class="average_daily_cost_data">58.61</td>
                  <td class="average_daily_pnl_data">0.27</td>
                  <td class="average_daily_mo_data">100.14</td>
                  <td class="average_daily_unreg_data">252.43</td>
                </tr>
                <tr class="roiRow">
                  <td>TH - Th-true-qr</td>

                  <td class="mo_data_0">0.00</td>
                  <td class="cost_data_0">0.00</td>
                  <td class="rev_data_0">52.14</td>
                  <td class="mo_data_1">0.00</td>
                  <td class="cost_data_1">0.00</td>
                  <td class="rev_data_1">5.87</td>

                  <td class="pnl_data">4.81</td>

                  <td class="unreg_data">2.00</td>

                  <td class="month_end_spending_data">0.00</td>

                  <td class="waki_gross_rev_data">0.00</td>

                  <td class="month_end_data">0.00</td>

                  <td class="comparison_mo_data">100.00</td>
                  <td class="comparison_cost_data">100.00</td>
                  <td class="comparison_rev_data">-788.06</td>
                  <td class="weekly_caps_data">0</td>
                  <td class="percent_caps_data">0</td>


                  <td class="roiprice_mo_data">0</td>
                  <td class="monthly_arpu_data">0</td>
                  <td class="roi_data_1">0</td>
                  <td class="subs_data">0</td>
                  <td class="average_daily_rev_data">7.45</td>
                  <td class="average_daily_cost_data">0</td>
                  <td class="average_daily_pnl_data">0</td>
                  <td class="average_daily_mo_data">0</td>
                  <td class="average_daily_unreg_data">0.29</td>
                </tr>
                <tr class="roiRow">
                  <td>VN - Viettel</td>

                  <td class="mo_data_0">0.00</td>
                  <td class="cost_data_0">0.00</td>
                  <td class="rev_data_0">60.64</td>
                  <td class="mo_data_1">0.00</td>
                  <td class="cost_data_1">0.00</td>
                  <td class="rev_data_1">5.67</td>

                  <td class="pnl_data">4.65</td>

                  <td class="unreg_data">17.00</td>

                  <td class="month_end_spending_data">0.00</td>

                  <td class="waki_gross_rev_data">0.00</td>

                  <td class="month_end_data">0.00</td>

                  <td class="comparison_mo_data">100.00</td>
                  <td class="comparison_cost_data">100.00</td>
                  <td class="comparison_rev_data">-969.52</td>
                  <td class="weekly_caps_data">0</td>
                  <td class="percent_caps_data">0</td>


                  <td class="roiprice_mo_data">0</td>
                  <td class="monthly_arpu_data">0</td>
                  <td class="roi_data_1">0</td>
                  <td class="subs_data">0</td>
                  <td class="average_daily_rev_data">8.66</td>
                  <td class="average_daily_cost_data">0</td>
                  <td class="average_daily_pnl_data">0</td>
                  <td class="average_daily_mo_data">0</td>
                  <td class="average_daily_unreg_data">2.43</td>
                </tr>
                <tr class="roiRow">
                  <td>ID - Id-smartfren-kb</td>

                  <td class="mo_data_0">1.00</td>
                  <td class="cost_data_0">0.20</td>
                  <td class="rev_data_0">43.14</td>
                  <td class="mo_data_1">0.00</td>
                  <td class="cost_data_1">0.00</td>
                  <td class="rev_data_1">4.78</td>

                  <td class="pnl_data">3.92</td>

                  <td class="unreg_data">154.00</td>

                  <td class="month_end_spending_data">0.20</td>

                  <td class="waki_gross_rev_data">0.00</td>

                  <td class="month_end_data">0.00</td>

                  <td class="comparison_mo_data">100.00</td>
                  <td class="comparison_cost_data">100.00</td>
                  <td class="comparison_rev_data">-802.31</td>
                  <td class="weekly_caps_data">0</td>
                  <td class="percent_caps_data">0</td>


                  <td class="roiprice_mo_data">0</td>
                  <td class="monthly_arpu_data">0</td>
                  <td class="roi_data_1">0</td>
                  <td class="subs_data">0</td>
                  <td class="average_daily_rev_data">6.16</td>
                  <td class="average_daily_cost_data">0.03</td>
                  <td class="average_daily_pnl_data">215.71</td>
                  <td class="average_daily_mo_data">0.14</td>
                  <td class="average_daily_unreg_data">22.00</td>
                </tr>
                <tr class="roiRow">
                  <td>KE - Safaricom</td>

                  <td class="mo_data_0">2.00</td>
                  <td class="cost_data_0">0.00</td>
                  <td class="rev_data_0">24.44</td>
                  <td class="mo_data_1">0.00</td>
                  <td class="cost_data_1">0.00</td>
                  <td class="rev_data_1">4.20</td>

                  <td class="pnl_data">3.24</td>

                  <td class="unreg_data">36.00</td>

                  <td class="month_end_spending_data">0.00</td>

                  <td class="waki_gross_rev_data">0.00</td>

                  <td class="month_end_data">0.00</td>

                  <td class="comparison_mo_data">100.00</td>
                  <td class="comparison_cost_data">100.00</td>
                  <td class="comparison_rev_data">-482.22</td>
                  <td class="weekly_caps_data">0</td>
                  <td class="percent_caps_data">0</td>


                  <td class="roiprice_mo_data">0</td>
                  <td class="monthly_arpu_data">0</td>
                  <td class="roi_data_1">0</td>
                  <td class="subs_data">0</td>
                  <td class="average_daily_rev_data">3.49</td>
                  <td class="average_daily_cost_data">0</td>
                  <td class="average_daily_pnl_data">0</td>
                  <td class="average_daily_mo_data">0.29</td>
                  <td class="average_daily_unreg_data">5.14</td>
                </tr>
                <tr class="roiRow">
                  <td>ID - Id-tri-yatta</td>

                  <td class="mo_data_0">0.00</td>
                  <td class="cost_data_0">0.00</td>
                  <td class="rev_data_0">47.82</td>
                  <td class="mo_data_1">0.00</td>
                  <td class="cost_data_1">0.00</td>
                  <td class="rev_data_1">4.09</td>

                  <td class="pnl_data">2.94</td>

                  <td class="unreg_data">15.00</td>

                  <td class="month_end_spending_data">0.00</td>

                  <td class="waki_gross_rev_data">0.00</td>

                  <td class="month_end_data">0.00</td>

                  <td class="comparison_mo_data">100.00</td>
                  <td class="comparison_cost_data">100.00</td>
                  <td class="comparison_rev_data">-1,070.00</td>
                  <td class="weekly_caps_data">0</td>
                  <td class="percent_caps_data">0</td>


                  <td class="roiprice_mo_data">0</td>
                  <td class="monthly_arpu_data">0</td>
                  <td class="roi_data_1">0</td>
                  <td class="subs_data">0</td>
                  <td class="average_daily_rev_data">6.83</td>
                  <td class="average_daily_cost_data">0</td>
                  <td class="average_daily_pnl_data">0</td>
                  <td class="average_daily_mo_data">0</td>
                  <td class="average_daily_unreg_data">2.14</td>
                </tr>
                <tr class="roiRow">
                  <td>KSA - Ksa-virgin-linkit</td>

                  <td class="mo_data_0">0.00</td>
                  <td class="cost_data_0">0.00</td>
                  <td class="rev_data_0">24.93</td>
                  <td class="mo_data_1">0.00</td>
                  <td class="cost_data_1">0.00</td>
                  <td class="rev_data_1">3.99</td>

                  <td class="pnl_data">2.87</td>

                  <td class="unreg_data">159.00</td>

                  <td class="month_end_spending_data">0.00</td>

                  <td class="waki_gross_rev_data">0.00</td>

                  <td class="month_end_data">0.00</td>

                  <td class="comparison_mo_data">100.00</td>
                  <td class="comparison_cost_data">100.00</td>
                  <td class="comparison_rev_data">-525.00</td>
                  <td class="weekly_caps_data">0</td>
                  <td class="percent_caps_data">0</td>


                  <td class="roiprice_mo_data">0</td>
                  <td class="monthly_arpu_data">0</td>
                  <td class="roi_data_1">0</td>
                  <td class="subs_data">0</td>
                  <td class="average_daily_rev_data">3.56</td>
                  <td class="average_daily_cost_data">0</td>
                  <td class="average_daily_pnl_data">0</td>
                  <td class="average_daily_mo_data">0</td>
                  <td class="average_daily_unreg_data">22.71</td>
                </tr>
                <tr class="roiRow">
                  <td>PL - Pol-orange-linkit</td>

                  <td class="mo_data_0">0.00</td>
                  <td class="cost_data_0">0.00</td>
                  <td class="rev_data_0">66.31</td>
                  <td class="mo_data_1">0.00</td>
                  <td class="cost_data_1">0.00</td>
                  <td class="rev_data_1">3.98</td>

                  <td class="pnl_data">2.86</td>

                  <td class="unreg_data">0.00</td>

                  <td class="month_end_spending_data">0.00</td>

                  <td class="waki_gross_rev_data">0.00</td>

                  <td class="month_end_data">0.00</td>

                  <td class="comparison_mo_data">100.00</td>
                  <td class="comparison_cost_data">100.00</td>
                  <td class="comparison_rev_data">-1,566.67</td>
                  <td class="weekly_caps_data">0</td>
                  <td class="percent_caps_data">0</td>


                  <td class="roiprice_mo_data">0</td>
                  <td class="monthly_arpu_data">0</td>
                  <td class="roi_data_1">0</td>
                  <td class="subs_data">0</td>
                  <td class="average_daily_rev_data">9.47</td>
                  <td class="average_daily_cost_data">0</td>
                  <td class="average_daily_pnl_data">0</td>
                  <td class="average_daily_mo_data">0</td>
                  <td class="average_daily_unreg_data">0</td>
                </tr>
                <tr class="roiRow">
                  <td>ZA - Za-mtn-mobixone</td>

                  <td class="mo_data_0">62.00</td>
                  <td class="cost_data_0">25.35</td>
                  <td class="rev_data_0">65.34</td>
                  <td class="mo_data_1">0.00</td>
                  <td class="cost_data_1">0.00</td>
                  <td class="rev_data_1">3.79</td>

                  <td class="pnl_data">3.10</td>

                  <td class="unreg_data">562.00</td>

                  <td class="month_end_spending_data">25.35</td>

                  <td class="waki_gross_rev_data">0.00</td>

                  <td class="month_end_data">0.00</td>

                  <td class="comparison_mo_data">100.00</td>
                  <td class="comparison_cost_data">100.00</td>
                  <td class="comparison_rev_data">-1,625.60</td>
                  <td class="weekly_caps_data">0</td>
                  <td class="percent_caps_data">0</td>


                  <td class="roiprice_mo_data">0</td>
                  <td class="monthly_arpu_data">0</td>
                  <td class="roi_data_1">0</td>
                  <td class="subs_data">0</td>
                  <td class="average_daily_rev_data">9.33</td>
                  <td class="average_daily_cost_data">3.62</td>
                  <td class="average_daily_pnl_data">2.58</td>
                  <td class="average_daily_mo_data">8.86</td>
                  <td class="average_daily_unreg_data">80.29</td>
                </tr>
                <tr class="roiRow">
                  <td>ID - Id-smartfren-pass</td>

                  <td class="mo_data_0">0.00</td>
                  <td class="cost_data_0">0.00</td>
                  <td class="rev_data_0">36.01</td>
                  <td class="mo_data_1">0.00</td>
                  <td class="cost_data_1">0.00</td>
                  <td class="rev_data_1">3.79</td>

                  <td class="pnl_data">3.10</td>

                  <td class="unreg_data">425.00</td>

                  <td class="month_end_spending_data">0.00</td>

                  <td class="waki_gross_rev_data">0.00</td>

                  <td class="month_end_data">0.00</td>

                  <td class="comparison_mo_data">100.00</td>
                  <td class="comparison_cost_data">100.00</td>
                  <td class="comparison_rev_data">-851.09</td>
                  <td class="weekly_caps_data">0</td>
                  <td class="percent_caps_data">0</td>


                  <td class="roiprice_mo_data">0</td>
                  <td class="monthly_arpu_data">0</td>
                  <td class="roi_data_1">0</td>
                  <td class="subs_data">0</td>
                  <td class="average_daily_rev_data">5.14</td>
                  <td class="average_daily_cost_data">0</td>
                  <td class="average_daily_pnl_data">0</td>
                  <td class="average_daily_mo_data">0</td>
                  <td class="average_daily_unreg_data">60.71</td>
                </tr>
                <tr class="roiRow">
                  <td>ID - Pass-tri-telesat</td>

                  <td class="mo_data_0">0.00</td>
                  <td class="cost_data_0">0.00</td>
                  <td class="rev_data_0">61.49</td>
                  <td class="mo_data_1">0.00</td>
                  <td class="cost_data_1">0.00</td>
                  <td class="rev_data_1">2.50</td>

                  <td class="pnl_data">1.80</td>

                  <td class="unreg_data">15.00</td>

                  <td class="month_end_spending_data">0.00</td>

                  <td class="waki_gross_rev_data">0.00</td>

                  <td class="month_end_data">0.00</td>

                  <td class="comparison_mo_data">100.00</td>
                  <td class="comparison_cost_data">100.00</td>
                  <td class="comparison_rev_data">-2,357.14</td>
                  <td class="weekly_caps_data">0</td>
                  <td class="percent_caps_data">0</td>


                  <td class="roiprice_mo_data">0</td>
                  <td class="monthly_arpu_data">0</td>
                  <td class="roi_data_1">0</td>
                  <td class="subs_data">0</td>
                  <td class="average_daily_rev_data">8.78</td>
                  <td class="average_daily_cost_data">0</td>
                  <td class="average_daily_pnl_data">0</td>
                  <td class="average_daily_mo_data">0</td>
                  <td class="average_daily_unreg_data">2.14</td>
                </tr>
                <tr class="roiRow">
                  <td>PS - Pse-ooredoo-linkit</td>

                  <td class="mo_data_0">0.00</td>
                  <td class="cost_data_0">0.00</td>
                  <td class="rev_data_0">17.12</td>
                  <td class="mo_data_1">0.00</td>
                  <td class="cost_data_1">0.00</td>
                  <td class="rev_data_1">2.39</td>

                  <td class="pnl_data">1.72</td>

                  <td class="unreg_data">7.00</td>

                  <td class="month_end_spending_data">0.00</td>

                  <td class="waki_gross_rev_data">0.00</td>

                  <td class="month_end_data">0.00</td>

                  <td class="comparison_mo_data">100.00</td>
                  <td class="comparison_cost_data">100.00</td>
                  <td class="comparison_rev_data">-616.67</td>
                  <td class="weekly_caps_data">0</td>
                  <td class="percent_caps_data">0</td>


                  <td class="roiprice_mo_data">0</td>
                  <td class="monthly_arpu_data">0</td>
                  <td class="roi_data_1">0</td>
                  <td class="subs_data">0</td>
                  <td class="average_daily_rev_data">2.45</td>
                  <td class="average_daily_cost_data">0</td>
                  <td class="average_daily_pnl_data">0</td>
                  <td class="average_daily_mo_data">0</td>
                  <td class="average_daily_unreg_data">1.00</td>
                </tr>
                <tr class="roiRow">
                  <td>ID - Kb-tri-telesat</td>

                  <td class="mo_data_0">0.00</td>
                  <td class="cost_data_0">0.00</td>
                  <td class="rev_data_0">33.61</td>
                  <td class="mo_data_1">0.00</td>
                  <td class="cost_data_1">0.00</td>
                  <td class="rev_data_1">1.63</td>

                  <td class="pnl_data">1.17</td>

                  <td class="unreg_data">36.00</td>

                  <td class="month_end_spending_data">0.00</td>

                  <td class="waki_gross_rev_data">0.00</td>

                  <td class="month_end_data">0.00</td>

                  <td class="comparison_mo_data">100.00</td>
                  <td class="comparison_cost_data">100.00</td>
                  <td class="comparison_rev_data">-1,968.00</td>
                  <td class="weekly_caps_data">0</td>
                  <td class="percent_caps_data">0</td>


                  <td class="roiprice_mo_data">0</td>
                  <td class="monthly_arpu_data">0</td>
                  <td class="roi_data_1">0</td>
                  <td class="subs_data">0</td>
                  <td class="average_daily_rev_data">4.80</td>
                  <td class="average_daily_cost_data">0</td>
                  <td class="average_daily_pnl_data">0</td>
                  <td class="average_daily_mo_data">0</td>
                  <td class="average_daily_unreg_data">5.14</td>
                </tr>
                <tr class="roiRow">
                  <td>KSA - Ksa-zein</td>

                  <td class="mo_data_0">279.00</td>
                  <td class="cost_data_0">174.20</td>
                  <td class="rev_data_0">13.51</td>
                  <td class="mo_data_1">47.00</td>
                  <td class="cost_data_1">36.40</td>
                  <td class="rev_data_1">0.85</td>

                  <td class="pnl_data">-35.77</td>

                  <td class="unreg_data">481.00</td>

                  <td class="month_end_spending_data">210.60</td>

                  <td class="waki_gross_rev_data">10.92</td>

                  <td class="month_end_data">3.28</td>

                  <td class="comparison_mo_data">-493.62</td>
                  <td class="comparison_cost_data">-378.57</td>
                  <td class="comparison_rev_data">-1,487.50</td>
                  <td class="weekly_caps_data">0</td>
                  <td class="percent_caps_data">0</td>


                  <td class="roiprice_mo_data">0.77</td>
                  <td class="monthly_arpu_data">0.0776</td>
                  <td class="roi_data_1">9.98</td>
                  <td class="subs_data">47.00</td>
                  <td class="average_daily_rev_data">1.93</td>
                  <td class="average_daily_cost_data">24.89</td>
                  <td class="average_daily_pnl_data">0.08</td>
                  <td class="average_daily_mo_data">39.86</td>
                  <td class="average_daily_unreg_data">68.71</td>
                </tr>
                <tr class="roiRow">
                  <td>LA - Ltc</td>

                  <td class="mo_data_0">53.00</td>
                  <td class="cost_data_0">15.60</td>
                  <td class="rev_data_0">6.59</td>
                  <td class="mo_data_1">4.00</td>
                  <td class="cost_data_1">1.04</td>
                  <td class="rev_data_1">0.84</td>

                  <td class="pnl_data">-0.49</td>

                  <td class="unreg_data">25.00</td>

                  <td class="month_end_spending_data">16.64</td>

                  <td class="waki_gross_rev_data">0.31</td>

                  <td class="month_end_data">0.09</td>

                  <td class="comparison_mo_data">-1,225.00</td>
                  <td class="comparison_cost_data">-1,400.00</td>
                  <td class="comparison_rev_data">-688.89</td>
                  <td class="weekly_caps_data">0</td>
                  <td class="percent_caps_data">0</td>


                  <td class="roiprice_mo_data">0.26</td>
                  <td class="monthly_arpu_data">0.8949</td>
                  <td class="roi_data_1">0.29</td>
                  <td class="subs_data">4.00</td>
                  <td class="average_daily_rev_data">0.94</td>
                  <td class="average_daily_cost_data">2.23</td>
                  <td class="average_daily_pnl_data">0.42</td>
                  <td class="average_daily_mo_data">7.57</td>
                  <td class="average_daily_unreg_data">3.57</td>
                </tr>
                <tr class="roiRow">
                  <td>KH - Smart</td>

                  <td class="mo_data_0">11.00</td>
                  <td class="cost_data_0">1.56</td>
                  <td class="rev_data_0">1,359.27</td>
                  <td class="mo_data_1">5.00</td>
                  <td class="cost_data_1">0.00</td>
                  <td class="rev_data_1">0.56</td>

                  <td class="pnl_data">0.35</td>

                  <td class="unreg_data">3.00</td>

                  <td class="month_end_spending_data">1.56</td>

                  <td class="waki_gross_rev_data">0.00</td>

                  <td class="month_end_data">0.00</td>

                  <td class="comparison_mo_data">-120.00</td>
                  <td class="comparison_cost_data">100.00</td>
                  <td class="comparison_rev_data">-241,119.88</td>
                  <td class="weekly_caps_data">0</td>
                  <td class="percent_caps_data">0</td>


                  <td class="roiprice_mo_data">0</td>
                  <td class="monthly_arpu_data">0.4830</td>
                  <td class="roi_data_1">0</td>
                  <td class="subs_data">5.00</td>
                  <td class="average_daily_rev_data">194.18</td>
                  <td class="average_daily_cost_data">0.22</td>
                  <td class="average_daily_pnl_data">871.33</td>
                  <td class="average_daily_mo_data">1.57</td>
                  <td class="average_daily_unreg_data">0.43</td>
                </tr>
                <tr class="roiRow">
                  <td>LA - Lao-tplus-linkit</td>

                  <td class="mo_data_0">72.00</td>
                  <td class="cost_data_0">22.36</td>
                  <td class="rev_data_0">5.44</td>
                  <td class="mo_data_1">2.00</td>
                  <td class="cost_data_1">0.52</td>
                  <td class="rev_data_1">0.49</td>

                  <td class="pnl_data">-0.17</td>

                  <td class="unreg_data">16.00</td>

                  <td class="month_end_spending_data">22.88</td>

                  <td class="waki_gross_rev_data">0.16</td>

                  <td class="month_end_data">0.05</td>

                  <td class="comparison_mo_data">-3,500.00</td>
                  <td class="comparison_cost_data">-4,200.00</td>
                  <td class="comparison_rev_data">-1,016.67</td>
                  <td class="weekly_caps_data">0</td>
                  <td class="percent_caps_data">0</td>


                  <td class="roiprice_mo_data">0.26</td>
                  <td class="monthly_arpu_data">1.0440</td>
                  <td class="roi_data_1">0.25</td>
                  <td class="subs_data">2.00</td>
                  <td class="average_daily_rev_data">0.78</td>
                  <td class="average_daily_cost_data">3.19</td>
                  <td class="average_daily_pnl_data">0.24</td>
                  <td class="average_daily_mo_data">10.29</td>
                  <td class="average_daily_unreg_data">2.29</td>
                </tr>
                <tr class="roiRow">
                  <td>EG - Eg-etisalat</td>

                  <td class="mo_data_0">0.00</td>
                  <td class="cost_data_0">0.00</td>
                  <td class="rev_data_0">4.64</td>
                  <td class="mo_data_1">0.00</td>
                  <td class="cost_data_1">0.00</td>
                  <td class="rev_data_1">0.41</td>

                  <td class="pnl_data">0.31</td>

                  <td class="unreg_data">29.00</td>

                  <td class="month_end_spending_data">0.00</td>

                  <td class="waki_gross_rev_data">0.00</td>

                  <td class="month_end_data">0.00</td>

                  <td class="comparison_mo_data">100.00</td>
                  <td class="comparison_cost_data">100.00</td>
                  <td class="comparison_rev_data">-1,021.56</td>
                  <td class="weekly_caps_data">0</td>
                  <td class="percent_caps_data">0</td>


                  <td class="roiprice_mo_data">0</td>
                  <td class="monthly_arpu_data">0</td>
                  <td class="roi_data_1">0</td>
                  <td class="subs_data">0</td>
                  <td class="average_daily_rev_data">0.66</td>
                  <td class="average_daily_cost_data">0</td>
                  <td class="average_daily_pnl_data">0</td>
                  <td class="average_daily_mo_data">0</td>
                  <td class="average_daily_unreg_data">4.14</td>
                </tr>
                <tr class="roiRow">
                  <td>CI - Ci-orange-linkit</td>

                  <td class="mo_data_0">0.00</td>
                  <td class="cost_data_0">0.00</td>
                  <td class="rev_data_0">1.51</td>
                  <td class="mo_data_1">0.00</td>
                  <td class="cost_data_1">0.00</td>
                  <td class="rev_data_1">0.18</td>

                  <td class="pnl_data">0.13</td>

                  <td class="unreg_data">3.00</td>

                  <td class="month_end_spending_data">0.00</td>

                  <td class="waki_gross_rev_data">0.00</td>

                  <td class="month_end_data">0.00</td>

                  <td class="comparison_mo_data">100.00</td>
                  <td class="comparison_cost_data">100.00</td>
                  <td class="comparison_rev_data">-750.00</td>
                  <td class="weekly_caps_data">0</td>
                  <td class="percent_caps_data">0</td>


                  <td class="roiprice_mo_data">0</td>
                  <td class="monthly_arpu_data">0</td>
                  <td class="roi_data_1">0</td>
                  <td class="subs_data">0</td>
                  <td class="average_daily_rev_data">0.22</td>
                  <td class="average_daily_cost_data">0</td>
                  <td class="average_daily_pnl_data">0</td>
                  <td class="average_daily_mo_data">0</td>
                  <td class="average_daily_unreg_data">0.43</td>
                </tr>
                <tr class="roiRow">
                  <td>PH - Ph-globe</td>

                  <td class="mo_data_0">0.00</td>
                  <td class="cost_data_0">0.00</td>
                  <td class="rev_data_0">0.38</td>
                  <td class="mo_data_1">0.00</td>
                  <td class="cost_data_1">0.00</td>
                  <td class="rev_data_1">0.07</td>

                  <td class="pnl_data">0.06</td>

                  <td class="unreg_data">0.00</td>

                  <td class="month_end_spending_data">0.00</td>

                  <td class="waki_gross_rev_data">0.00</td>

                  <td class="month_end_data">0.00</td>

                  <td class="comparison_mo_data">100.00</td>
                  <td class="comparison_cost_data">100.00</td>
                  <td class="comparison_rev_data">-450.00</td>
                  <td class="weekly_caps_data">0</td>
                  <td class="percent_caps_data">0</td>


                  <td class="roiprice_mo_data">0</td>
                  <td class="monthly_arpu_data">0</td>
                  <td class="roi_data_1">0</td>
                  <td class="subs_data">0</td>
                  <td class="average_daily_rev_data">0.05</td>
                  <td class="average_daily_cost_data">0</td>
                  <td class="average_daily_pnl_data">0</td>
                  <td class="average_daily_mo_data">0</td>
                  <td class="average_daily_unreg_data">0</td>
                </tr>
                <tr class="roiRow">
                  <td>ID - Waki-tri-telesat</td>

                  <td class="mo_data_0">0.00</td>
                  <td class="cost_data_0">0.00</td>
                  <td class="rev_data_0">1.38</td>
                  <td class="mo_data_1">0.00</td>
                  <td class="cost_data_1">0.00</td>
                  <td class="rev_data_1">0.05</td>

                  <td class="pnl_data">0.04</td>

                  <td class="unreg_data">0.00</td>

                  <td class="month_end_spending_data">0.00</td>

                  <td class="waki_gross_rev_data">0.00</td>

                  <td class="month_end_data">0.00</td>

                  <td class="comparison_mo_data">100.00</td>
                  <td class="comparison_cost_data">100.00</td>
                  <td class="comparison_rev_data">-2,550.00</td>
                  <td class="weekly_caps_data">0</td>
                  <td class="percent_caps_data">0</td>


                  <td class="roiprice_mo_data">0</td>
                  <td class="monthly_arpu_data">0</td>
                  <td class="roi_data_1">0</td>
                  <td class="subs_data">0</td>
                  <td class="average_daily_rev_data">0.20</td>
                  <td class="average_daily_cost_data">0</td>
                  <td class="average_daily_pnl_data">0</td>
                  <td class="average_daily_mo_data">0</td>
                  <td class="average_daily_unreg_data">0</td>
                </tr>
                <tr class="roiRow">
                  <td>ID - Telkomsel</td>

                  <td class="mo_data_0">4,184.00</td>
                  <td class="cost_data_0">416.80</td>
                  <td class="rev_data_0">0.94</td>
                  <td class="mo_data_1">217.00</td>
                  <td class="cost_data_1">20.50</td>
                  <td class="rev_data_1">0.03</td>

                  <td class="pnl_data">-20.47</td>

                  <td class="unreg_data">4.00</td>

                  <td class="month_end_spending_data">437.30</td>

                  <td class="waki_gross_rev_data">6.15</td>

                  <td class="month_end_data">1.85</td>

                  <td class="comparison_mo_data">-1,828.11</td>
                  <td class="comparison_cost_data">-1,933.17</td>
                  <td class="comparison_rev_data">-2,800.00</td>
                  <td class="weekly_caps_data">0</td>
                  <td class="percent_caps_data">0</td>


                  <td class="roiprice_mo_data">0.09</td>
                  <td class="monthly_arpu_data">0.0006</td>
                  <td class="roi_data_1">147.18</td>
                  <td class="subs_data">217.00</td>
                  <td class="average_daily_rev_data">0.13</td>
                  <td class="average_daily_cost_data">59.54</td>
                  <td class="average_daily_pnl_data">0.00</td>
                  <td class="average_daily_mo_data">597.71</td>
                  <td class="average_daily_unreg_data">0.57</td>
                </tr>
                <tr class="roiRow">
                  <td>TH - Ais</td>

                  <td class="mo_data_0">0.00</td>
                  <td class="cost_data_0">0.00</td>
                  <td class="rev_data_0">0.00</td>
                  <td class="mo_data_1">0.00</td>
                  <td class="cost_data_1">0.00</td>
                  <td class="rev_data_1">0.00</td>

                  <td class="pnl_data">0.00</td>

                  <td class="unreg_data">0.00</td>

                  <td class="month_end_spending_data">0.00</td>

                  <td class="waki_gross_rev_data">0.00</td>

                  <td class="month_end_data">0.00</td>

                  <td class="comparison_mo_data">100.00</td>
                  <td class="comparison_cost_data">100.00</td>
                  <td class="comparison_rev_data">100.00</td>
                  <td class="weekly_caps_data">0</td>
                  <td class="percent_caps_data">0</td>


                  <td class="roiprice_mo_data">0</td>
                  <td class="monthly_arpu_data">0</td>
                  <td class="roi_data_1">0</td>
                  <td class="subs_data">0</td>
                  <td class="average_daily_rev_data">0</td>
                  <td class="average_daily_cost_data">0</td>
                  <td class="average_daily_pnl_data">0</td>
                  <td class="average_daily_mo_data">0</td>
                  <td class="average_daily_unreg_data">0</td>
                </tr>
                <tr class="roiRow">
                  <td>KW - Viva</td>

                  <td class="mo_data_0">0.00</td>
                  <td class="cost_data_0">0.00</td>
                  <td class="rev_data_0">0.00</td>
                  <td class="mo_data_1">0.00</td>
                  <td class="cost_data_1">0.00</td>
                  <td class="rev_data_1">0.00</td>

                  <td class="pnl_data">-5.45</td>

                  <td class="unreg_data">0.00</td>

                  <td class="month_end_spending_data">0.00</td>

                  <td class="waki_gross_rev_data">0.00</td>

                  <td class="month_end_data">0.00</td>

                  <td class="comparison_mo_data">100.00</td>
                  <td class="comparison_cost_data">100.00</td>
                  <td class="comparison_rev_data">100.00</td>
                  <td class="weekly_caps_data">0</td>
                  <td class="percent_caps_data">0</td>


                  <td class="roiprice_mo_data">0</td>
                  <td class="monthly_arpu_data">0</td>
                  <td class="roi_data_1">0</td>
                  <td class="subs_data">0</td>
                  <td class="average_daily_rev_data">0</td>
                  <td class="average_daily_cost_data">0</td>
                  <td class="average_daily_pnl_data">0</td>
                  <td class="average_daily_mo_data">0</td>
                  <td class="average_daily_unreg_data">0</td>
                </tr>
                <tr class="roiRow">
                  <td>KH - Cellcard</td>

                  <td class="mo_data_0">0.00</td>
                  <td class="cost_data_0">0.00</td>
                  <td class="rev_data_0">0.00</td>
                  <td class="mo_data_1">0.00</td>
                  <td class="cost_data_1">0.00</td>
                  <td class="rev_data_1">0.00</td>

                  <td class="pnl_data">0.00</td>

                  <td class="unreg_data">0.00</td>

                  <td class="month_end_spending_data">0.00</td>

                  <td class="waki_gross_rev_data">0.00</td>

                  <td class="month_end_data">0.00</td>

                  <td class="comparison_mo_data">100.00</td>
                  <td class="comparison_cost_data">100.00</td>
                  <td class="comparison_rev_data">100.00</td>
                  <td class="weekly_caps_data">0</td>
                  <td class="percent_caps_data">0</td>


                  <td class="roiprice_mo_data">0</td>
                  <td class="monthly_arpu_data">0</td>
                  <td class="roi_data_1">0</td>
                  <td class="subs_data">0</td>
                  <td class="average_daily_rev_data">0</td>
                  <td class="average_daily_cost_data">0</td>
                  <td class="average_daily_pnl_data">0</td>
                  <td class="average_daily_mo_data">0</td>
                  <td class="average_daily_unreg_data">0</td>
                </tr>
                <tr class="roiRow">
                  <td>AE - Etisalat-knc</td>

                  <td class="mo_data_0">3.00</td>
                  <td class="cost_data_0">0.00</td>
                  <td class="rev_data_0">13.88</td>
                  <td class="mo_data_1">0.00</td>
                  <td class="cost_data_1">0.00</td>
                  <td class="rev_data_1">0.00</td>

                  <td class="pnl_data">0.00</td>

                  <td class="unreg_data">1.00</td>

                  <td class="month_end_spending_data">0.00</td>

                  <td class="waki_gross_rev_data">0.00</td>

                  <td class="month_end_data">0.00</td>

                  <td class="comparison_mo_data">100.00</td>
                  <td class="comparison_cost_data">100.00</td>
                  <td class="comparison_rev_data">100.00</td>
                  <td class="weekly_caps_data">0</td>
                  <td class="percent_caps_data">0</td>


                  <td class="roiprice_mo_data">0</td>
                  <td class="monthly_arpu_data">0</td>
                  <td class="roi_data_1">0</td>
                  <td class="subs_data">0</td>
                  <td class="average_daily_rev_data">1.98</td>
                  <td class="average_daily_cost_data">0</td>
                  <td class="average_daily_pnl_data">0</td>
                  <td class="average_daily_mo_data">0.43</td>
                  <td class="average_daily_unreg_data">0.14</td>
                </tr>
                <tr class="roiRow">
                  <td>TH - True-cyb</td>

                  <td class="mo_data_0">219.00</td>
                  <td class="cost_data_0">45.76</td>
                  <td class="rev_data_0">172.00</td>
                  <td class="mo_data_1">0.00</td>
                  <td class="cost_data_1">0.00</td>
                  <td class="rev_data_1">0.00</td>

                  <td class="pnl_data">0.00</td>

                  <td class="unreg_data">495.00</td>

                  <td class="month_end_spending_data">45.76</td>

                  <td class="waki_gross_rev_data">0.00</td>

                  <td class="month_end_data">0.00</td>

                  <td class="comparison_mo_data">100.00</td>
                  <td class="comparison_cost_data">100.00</td>
                  <td class="comparison_rev_data">100.00</td>
                  <td class="weekly_caps_data">0</td>
                  <td class="percent_caps_data">0</td>


                  <td class="roiprice_mo_data">0</td>
                  <td class="monthly_arpu_data">0</td>
                  <td class="roi_data_1">0</td>
                  <td class="subs_data">0</td>
                  <td class="average_daily_rev_data">24.57</td>
                  <td class="average_daily_cost_data">6.54</td>
                  <td class="average_daily_pnl_data">3.76</td>
                  <td class="average_daily_mo_data">31.29</td>
                  <td class="average_daily_unreg_data">70.71</td>
                </tr>
                <tr class="roiRow">
                  <td>VN - Vietnamobile</td>

                  <td class="mo_data_0">0.00</td>
                  <td class="cost_data_0">0.00</td>
                  <td class="rev_data_0">0.00</td>
                  <td class="mo_data_1">0.00</td>
                  <td class="cost_data_1">0.00</td>
                  <td class="rev_data_1">0.00</td>

                  <td class="pnl_data">0.00</td>

                  <td class="unreg_data">0.00</td>

                  <td class="month_end_spending_data">0.00</td>

                  <td class="waki_gross_rev_data">0.00</td>

                  <td class="month_end_data">0.00</td>

                  <td class="comparison_mo_data">100.00</td>
                  <td class="comparison_cost_data">100.00</td>
                  <td class="comparison_rev_data">100.00</td>
                  <td class="weekly_caps_data">0</td>
                  <td class="percent_caps_data">0</td>


                  <td class="roiprice_mo_data">0</td>
                  <td class="monthly_arpu_data">0</td>
                  <td class="roi_data_1">0</td>
                  <td class="subs_data">0</td>
                  <td class="average_daily_rev_data">0</td>
                  <td class="average_daily_cost_data">0</td>
                  <td class="average_daily_pnl_data">0</td>
                  <td class="average_daily_mo_data">0</td>
                  <td class="average_daily_unreg_data">0</td>
                </tr>
                <tr class="roiRow">
                  <td>QA - Ooredoo</td>

                  <td class="mo_data_0">0.00</td>
                  <td class="cost_data_0">0.00</td>
                  <td class="rev_data_0">0.00</td>
                  <td class="mo_data_1">0.00</td>
                  <td class="cost_data_1">0.00</td>
                  <td class="rev_data_1">0.00</td>

                  <td class="pnl_data">0.00</td>

                  <td class="unreg_data">0.00</td>

                  <td class="month_end_spending_data">0.00</td>

                  <td class="waki_gross_rev_data">0.00</td>

                  <td class="month_end_data">0.00</td>

                  <td class="comparison_mo_data">100.00</td>
                  <td class="comparison_cost_data">100.00</td>
                  <td class="comparison_rev_data">100.00</td>
                  <td class="weekly_caps_data">0</td>
                  <td class="percent_caps_data">0</td>


                  <td class="roiprice_mo_data">0</td>
                  <td class="monthly_arpu_data">0</td>
                  <td class="roi_data_1">0</td>
                  <td class="subs_data">0</td>
                  <td class="average_daily_rev_data">0</td>
                  <td class="average_daily_cost_data">0</td>
                  <td class="average_daily_pnl_data">0</td>
                  <td class="average_daily_mo_data">0</td>
                  <td class="average_daily_unreg_data">0</td>
                </tr>
                <tr class="roiRow">
                  <td>ID - Id-xl-kb</td>

                  <td class="mo_data_0">0.00</td>
                  <td class="cost_data_0">0.00</td>
                  <td class="rev_data_0">0.00</td>
                  <td class="mo_data_1">0.00</td>
                  <td class="cost_data_1">0.00</td>
                  <td class="rev_data_1">0.00</td>

                  <td class="pnl_data">0.00</td>

                  <td class="unreg_data">333.00</td>

                  <td class="month_end_spending_data">0.00</td>

                  <td class="waki_gross_rev_data">0.00</td>

                  <td class="month_end_data">0.00</td>

                  <td class="comparison_mo_data">100.00</td>
                  <td class="comparison_cost_data">100.00</td>
                  <td class="comparison_rev_data">100.00</td>
                  <td class="weekly_caps_data">0</td>
                  <td class="percent_caps_data">0</td>


                  <td class="roiprice_mo_data">0</td>
                  <td class="monthly_arpu_data">0</td>
                  <td class="roi_data_1">0</td>
                  <td class="subs_data">0</td>
                  <td class="average_daily_rev_data">0</td>
                  <td class="average_daily_cost_data">0</td>
                  <td class="average_daily_pnl_data">0</td>
                  <td class="average_daily_mo_data">0</td>
                  <td class="average_daily_unreg_data">47.57</td>
                </tr>
                <tr class="roiRow">
                  <td>EG - Eg-vodafone</td>

                  <td class="mo_data_0">0.00</td>
                  <td class="cost_data_0">0.00</td>
                  <td class="rev_data_0">0.00</td>
                  <td class="mo_data_1">0.00</td>
                  <td class="cost_data_1">0.00</td>
                  <td class="rev_data_1">0.00</td>

                  <td class="pnl_data">0.00</td>

                  <td class="unreg_data">958.00</td>

                  <td class="month_end_spending_data">0.00</td>

                  <td class="waki_gross_rev_data">0.00</td>

                  <td class="month_end_data">0.00</td>

                  <td class="comparison_mo_data">100.00</td>
                  <td class="comparison_cost_data">100.00</td>
                  <td class="comparison_rev_data">100.00</td>
                  <td class="weekly_caps_data">0</td>
                  <td class="percent_caps_data">0</td>


                  <td class="roiprice_mo_data">0</td>
                  <td class="monthly_arpu_data">0</td>
                  <td class="roi_data_1">0</td>
                  <td class="subs_data">0</td>
                  <td class="average_daily_rev_data">0</td>
                  <td class="average_daily_cost_data">0</td>
                  <td class="average_daily_pnl_data">0</td>
                  <td class="average_daily_mo_data">0</td>
                  <td class="average_daily_unreg_data">136.86</td>
                </tr>
                <tr class="roiRow">
                  <td>VN - Mobifone</td>

                  <td class="mo_data_0">0.00</td>
                  <td class="cost_data_0">0.00</td>
                  <td class="rev_data_0">0.00</td>
                  <td class="mo_data_1">0.00</td>
                  <td class="cost_data_1">0.00</td>
                  <td class="rev_data_1">0.00</td>

                  <td class="pnl_data">0.00</td>

                  <td class="unreg_data">2.00</td>

                  <td class="month_end_spending_data">0.00</td>

                  <td class="waki_gross_rev_data">0.00</td>

                  <td class="month_end_data">0.00</td>

                  <td class="comparison_mo_data">100.00</td>
                  <td class="comparison_cost_data">100.00</td>
                  <td class="comparison_rev_data">100.00</td>
                  <td class="weekly_caps_data">0</td>
                  <td class="percent_caps_data">0</td>


                  <td class="roiprice_mo_data">0</td>
                  <td class="monthly_arpu_data">0</td>
                  <td class="roi_data_1">0</td>
                  <td class="subs_data">0</td>
                  <td class="average_daily_rev_data">0</td>
                  <td class="average_daily_cost_data">0</td>
                  <td class="average_daily_pnl_data">0</td>
                  <td class="average_daily_mo_data">0</td>
                  <td class="average_daily_unreg_data">0.29</td>
                </tr>
                <tr class="roiRow">
                  <td>GH - Mtnghana</td>

                  <td class="mo_data_0">0.00</td>
                  <td class="cost_data_0">0.00</td>
                  <td class="rev_data_0">0.00</td>
                  <td class="mo_data_1">0.00</td>
                  <td class="cost_data_1">0.00</td>
                  <td class="rev_data_1">0.00</td>

                  <td class="pnl_data">0.00</td>

                  <td class="unreg_data">0.00</td>

                  <td class="month_end_spending_data">0.00</td>

                  <td class="waki_gross_rev_data">0.00</td>

                  <td class="month_end_data">0.00</td>

                  <td class="comparison_mo_data">100.00</td>
                  <td class="comparison_cost_data">100.00</td>
                  <td class="comparison_rev_data">100.00</td>
                  <td class="weekly_caps_data">0</td>
                  <td class="percent_caps_data">0</td>


                  <td class="roiprice_mo_data">0</td>
                  <td class="monthly_arpu_data">0</td>
                  <td class="roi_data_1">0</td>
                  <td class="subs_data">0</td>
                  <td class="average_daily_rev_data">0</td>
                  <td class="average_daily_cost_data">0</td>
                  <td class="average_daily_pnl_data">0</td>
                  <td class="average_daily_mo_data">0</td>
                  <td class="average_daily_unreg_data">0</td>
                </tr>
                <tr class="roiRow">
                  <td>KH - Metfone</td>

                  <td class="mo_data_0">0.00</td>
                  <td class="cost_data_0">0.00</td>
                  <td class="rev_data_0">0.00</td>
                  <td class="mo_data_1">0.00</td>
                  <td class="cost_data_1">0.00</td>
                  <td class="rev_data_1">0.00</td>

                  <td class="pnl_data">0.00</td>

                  <td class="unreg_data">0.00</td>

                  <td class="month_end_spending_data">0.00</td>

                  <td class="waki_gross_rev_data">0.00</td>

                  <td class="month_end_data">0.00</td>

                  <td class="comparison_mo_data">100.00</td>
                  <td class="comparison_cost_data">100.00</td>
                  <td class="comparison_rev_data">100.00</td>
                  <td class="weekly_caps_data">0</td>
                  <td class="percent_caps_data">0</td>


                  <td class="roiprice_mo_data">0</td>
                  <td class="monthly_arpu_data">0</td>
                  <td class="roi_data_1">0</td>
                  <td class="subs_data">0</td>
                  <td class="average_daily_rev_data">0</td>
                  <td class="average_daily_cost_data">0</td>
                  <td class="average_daily_pnl_data">0</td>
                  <td class="average_daily_mo_data">0</td>
                  <td class="average_daily_unreg_data">0</td>
                </tr>
                <tr class="roiRow">
                  <td>MM - My-gtmh-linkit</td>

                  <td class="mo_data_0">0.00</td>
                  <td class="cost_data_0">0.00</td>
                  <td class="rev_data_0">0.00</td>
                  <td class="mo_data_1">0.00</td>
                  <td class="cost_data_1">0.00</td>
                  <td class="rev_data_1">0.00</td>

                  <td class="pnl_data">0.00</td>

                  <td class="unreg_data">0.00</td>

                  <td class="month_end_spending_data">0.00</td>

                  <td class="waki_gross_rev_data">0.00</td>

                  <td class="month_end_data">0.00</td>

                  <td class="comparison_mo_data">100.00</td>
                  <td class="comparison_cost_data">100.00</td>
                  <td class="comparison_rev_data">100.00</td>
                  <td class="weekly_caps_data">0</td>
                  <td class="percent_caps_data">0</td>


                  <td class="roiprice_mo_data">0</td>
                  <td class="monthly_arpu_data">0</td>
                  <td class="roi_data_1">0</td>
                  <td class="subs_data">0</td>
                  <td class="average_daily_rev_data">0</td>
                  <td class="average_daily_cost_data">0</td>
                  <td class="average_daily_pnl_data">0</td>
                  <td class="average_daily_mo_data">0</td>
                  <td class="average_daily_unreg_data">0</td>
                </tr>
                <tr class="roiRow">
                  <td>ID - Linkit-rbt-isat</td>

                  <td class="mo_data_0">0.00</td>
                  <td class="cost_data_0">0.00</td>
                  <td class="rev_data_0">0.00</td>
                  <td class="mo_data_1">0.00</td>
                  <td class="cost_data_1">0.00</td>
                  <td class="rev_data_1">0.00</td>

                  <td class="pnl_data">0.00</td>

                  <td class="unreg_data">0.00</td>

                  <td class="month_end_spending_data">0.00</td>

                  <td class="waki_gross_rev_data">0.00</td>

                  <td class="month_end_data">0.00</td>

                  <td class="comparison_mo_data">100.00</td>
                  <td class="comparison_cost_data">100.00</td>
                  <td class="comparison_rev_data">100.00</td>
                  <td class="weekly_caps_data">0</td>
                  <td class="percent_caps_data">0</td>


                  <td class="roiprice_mo_data">0</td>
                  <td class="monthly_arpu_data">0</td>
                  <td class="roi_data_1">0</td>
                  <td class="subs_data">0</td>
                  <td class="average_daily_rev_data">0</td>
                  <td class="average_daily_cost_data">0</td>
                  <td class="average_daily_pnl_data">0</td>
                  <td class="average_daily_mo_data">0</td>
                  <td class="average_daily_unreg_data">0</td>
                </tr>
                <tr class="roiRow">
                  <td>ZA - Za-vodacom-mobixone</td>

                  <td class="mo_data_0">23.00</td>
                  <td class="cost_data_0">13.00</td>
                  <td class="rev_data_0">204.49</td>
                  <td class="mo_data_1">0.00</td>
                  <td class="cost_data_1">0.00</td>
                  <td class="rev_data_1">0.00</td>

                  <td class="pnl_data">0.00</td>

                  <td class="unreg_data">114.00</td>

                  <td class="month_end_spending_data">13.00</td>

                  <td class="waki_gross_rev_data">0.00</td>

                  <td class="month_end_data">0.00</td>

                  <td class="comparison_mo_data">100.00</td>
                  <td class="comparison_cost_data">100.00</td>
                  <td class="comparison_rev_data">100.00</td>
                  <td class="weekly_caps_data">0</td>
                  <td class="percent_caps_data">0</td>


                  <td class="roiprice_mo_data">0</td>
                  <td class="monthly_arpu_data">0</td>
                  <td class="roi_data_1">0</td>
                  <td class="subs_data">0</td>
                  <td class="average_daily_rev_data">29.21</td>
                  <td class="average_daily_cost_data">1.86</td>
                  <td class="average_daily_pnl_data">15.73</td>
                  <td class="average_daily_mo_data">3.29</td>
                  <td class="average_daily_unreg_data">16.29</td>
                </tr>
                <tr class="roiRow">
                  <td>ID - Yatta-rbt-isat</td>

                  <td class="mo_data_0">0.00</td>
                  <td class="cost_data_0">0.00</td>
                  <td class="rev_data_0">0.00</td>
                  <td class="mo_data_1">0.00</td>
                  <td class="cost_data_1">0.00</td>
                  <td class="rev_data_1">0.00</td>

                  <td class="pnl_data">0.00</td>

                  <td class="unreg_data">0.00</td>

                  <td class="month_end_spending_data">0.00</td>

                  <td class="waki_gross_rev_data">0.00</td>

                  <td class="month_end_data">0.00</td>

                  <td class="comparison_mo_data">100.00</td>
                  <td class="comparison_cost_data">100.00</td>
                  <td class="comparison_rev_data">100.00</td>
                  <td class="weekly_caps_data">0</td>
                  <td class="percent_caps_data">0</td>


                  <td class="roiprice_mo_data">0</td>
                  <td class="monthly_arpu_data">0</td>
                  <td class="roi_data_1">0</td>
                  <td class="subs_data">0</td>
                  <td class="average_daily_rev_data">0</td>
                  <td class="average_daily_cost_data">0</td>
                  <td class="average_daily_pnl_data">0</td>
                  <td class="average_daily_mo_data">0</td>
                  <td class="average_daily_unreg_data">0</td>
                </tr>
                <tr class="roiRow">
                  <td>ID - Waki-rbt-isat</td>

                  <td class="mo_data_0">0.00</td>
                  <td class="cost_data_0">0.00</td>
                  <td class="rev_data_0">0.00</td>
                  <td class="mo_data_1">0.00</td>
                  <td class="cost_data_1">0.00</td>
                  <td class="rev_data_1">0.00</td>

                  <td class="pnl_data">0.00</td>

                  <td class="unreg_data">0.00</td>

                  <td class="month_end_spending_data">0.00</td>

                  <td class="waki_gross_rev_data">0.00</td>

                  <td class="month_end_data">0.00</td>

                  <td class="comparison_mo_data">100.00</td>
                  <td class="comparison_cost_data">100.00</td>
                  <td class="comparison_rev_data">100.00</td>
                  <td class="weekly_caps_data">0</td>
                  <td class="percent_caps_data">0</td>


                  <td class="roiprice_mo_data">0</td>
                  <td class="monthly_arpu_data">0</td>
                  <td class="roi_data_1">0</td>
                  <td class="subs_data">0</td>
                  <td class="average_daily_rev_data">0</td>
                  <td class="average_daily_cost_data">0</td>
                  <td class="average_daily_pnl_data">0</td>
                  <td class="average_daily_mo_data">0</td>
                  <td class="average_daily_unreg_data">0</td>
                </tr>
                <tr class="roiRow">
                  <td>UK - Ccstrrp</td>

                  <td class="mo_data_0">0.00</td>
                  <td class="cost_data_0">0.00</td>
                  <td class="rev_data_0">0.00</td>
                  <td class="mo_data_1">0.00</td>
                  <td class="cost_data_1">0.00</td>
                  <td class="rev_data_1">0.00</td>

                  <td class="pnl_data">0.00</td>

                  <td class="unreg_data">0.00</td>

                  <td class="month_end_spending_data">0.00</td>

                  <td class="waki_gross_rev_data">0.00</td>

                  <td class="month_end_data">0.00</td>

                  <td class="comparison_mo_data">100.00</td>
                  <td class="comparison_cost_data">100.00</td>
                  <td class="comparison_rev_data">100.00</td>
                  <td class="weekly_caps_data">0</td>
                  <td class="percent_caps_data">0</td>


                  <td class="roiprice_mo_data">0</td>
                  <td class="monthly_arpu_data">0</td>
                  <td class="roi_data_1">0</td>
                  <td class="subs_data">0</td>
                  <td class="average_daily_rev_data">0</td>
                  <td class="average_daily_cost_data">0</td>
                  <td class="average_daily_pnl_data">0</td>
                  <td class="average_daily_mo_data">0</td>
                  <td class="average_daily_unreg_data">0</td>
                </tr>
                <tr class="roiRow">
                  <td>KSA - Ksa-stc</td>

                  <td class="mo_data_0">36.00</td>
                  <td class="cost_data_0">20.93</td>
                  <td class="rev_data_0">0.00</td>
                  <td class="mo_data_1">2.00</td>
                  <td class="cost_data_1">0.00</td>
                  <td class="rev_data_1">0.00</td>

                  <td class="pnl_data">0.00</td>

                  <td class="unreg_data">108.00</td>

                  <td class="month_end_spending_data">20.93</td>

                  <td class="waki_gross_rev_data">0.00</td>

                  <td class="month_end_data">0.00</td>

                  <td class="comparison_mo_data">-1,700.00</td>
                  <td class="comparison_cost_data">100.00</td>
                  <td class="comparison_rev_data">100.00</td>
                  <td class="weekly_caps_data">0</td>
                  <td class="percent_caps_data">0</td>


                  <td class="roiprice_mo_data">0</td>
                  <td class="monthly_arpu_data">0</td>
                  <td class="roi_data_1">0</td>
                  <td class="subs_data">2.00</td>
                  <td class="average_daily_rev_data">0</td>
                  <td class="average_daily_cost_data">2.99</td>
                  <td class="average_daily_pnl_data">0</td>
                  <td class="average_daily_mo_data">5.14</td>
                  <td class="average_daily_unreg_data">15.43</td>
                </tr>
                <tr class="roiRow">
                  <td>GH - Gha-vodafone-linkit</td>

                  <td class="mo_data_0">0.00</td>
                  <td class="cost_data_0">0.00</td>
                  <td class="rev_data_0">0.00</td>
                  <td class="mo_data_1">0.00</td>
                  <td class="cost_data_1">0.00</td>
                  <td class="rev_data_1">0.00</td>

                  <td class="pnl_data">0.00</td>

                  <td class="unreg_data">0.00</td>

                  <td class="month_end_spending_data">0.00</td>

                  <td class="waki_gross_rev_data">0.00</td>

                  <td class="month_end_data">0.00</td>

                  <td class="comparison_mo_data">100.00</td>
                  <td class="comparison_cost_data">100.00</td>
                  <td class="comparison_rev_data">100.00</td>
                  <td class="weekly_caps_data">0</td>
                  <td class="percent_caps_data">0</td>


                  <td class="roiprice_mo_data">0</td>
                  <td class="monthly_arpu_data">0</td>
                  <td class="roi_data_1">0</td>
                  <td class="subs_data">0</td>
                  <td class="average_daily_rev_data">0</td>
                  <td class="average_daily_cost_data">0</td>
                  <td class="average_daily_pnl_data">0</td>
                  <td class="average_daily_mo_data">0</td>
                  <td class="average_daily_unreg_data">0</td>
                </tr>
                <tr class="roiRow">
                  <td>HT - Hti-natcom-linkit</td>

                  <td class="mo_data_0">0.00</td>
                  <td class="cost_data_0">0.00</td>
                  <td class="rev_data_0">0.00</td>
                  <td class="mo_data_1">0.00</td>
                  <td class="cost_data_1">0.00</td>
                  <td class="rev_data_1">0.00</td>

                  <td class="pnl_data">0.00</td>

                  <td class="unreg_data">1.00</td>

                  <td class="month_end_spending_data">0.00</td>

                  <td class="waki_gross_rev_data">0.00</td>

                  <td class="month_end_data">0.00</td>

                  <td class="comparison_mo_data">100.00</td>
                  <td class="comparison_cost_data">100.00</td>
                  <td class="comparison_rev_data">100.00</td>
                  <td class="weekly_caps_data">0</td>
                  <td class="percent_caps_data">0</td>


                  <td class="roiprice_mo_data">0</td>
                  <td class="monthly_arpu_data">0</td>
                  <td class="roi_data_1">0</td>
                  <td class="subs_data">0</td>
                  <td class="average_daily_rev_data">0</td>
                  <td class="average_daily_cost_data">0</td>
                  <td class="average_daily_pnl_data">0</td>
                  <td class="average_daily_mo_data">0</td>
                  <td class="average_daily_unreg_data">0.14</td>
                </tr>
                <tr class="roiRow">
                  <td>CZ - Cze-nth-linkit</td>

                  <td class="mo_data_0">0.00</td>
                  <td class="cost_data_0">0.00</td>
                  <td class="rev_data_0">0.00</td>
                  <td class="mo_data_1">0.00</td>
                  <td class="cost_data_1">0.00</td>
                  <td class="rev_data_1">0.00</td>

                  <td class="pnl_data">0.00</td>

                  <td class="unreg_data">0.00</td>

                  <td class="month_end_spending_data">0.00</td>

                  <td class="waki_gross_rev_data">0.00</td>

                  <td class="month_end_data">0.00</td>

                  <td class="comparison_mo_data">100.00</td>
                  <td class="comparison_cost_data">100.00</td>
                  <td class="comparison_rev_data">100.00</td>
                  <td class="weekly_caps_data">0</td>
                  <td class="percent_caps_data">0</td>


                  <td class="roiprice_mo_data">0</td>
                  <td class="monthly_arpu_data">0</td>
                  <td class="roi_data_1">0</td>
                  <td class="subs_data">0</td>
                  <td class="average_daily_rev_data">0</td>
                  <td class="average_daily_cost_data">0</td>
                  <td class="average_daily_pnl_data">0</td>
                  <td class="average_daily_mo_data">0</td>
                  <td class="average_daily_unreg_data">0</td>
                </tr>
                <tr class="roiRow">
                  <td>ID - Pass-rbt-isat</td>

                  <td class="mo_data_0">0.00</td>
                  <td class="cost_data_0">0.00</td>
                  <td class="rev_data_0">0.00</td>
                  <td class="mo_data_1">0.00</td>
                  <td class="cost_data_1">0.00</td>
                  <td class="rev_data_1">0.00</td>

                  <td class="pnl_data">0.00</td>

                  <td class="unreg_data">0.00</td>

                  <td class="month_end_spending_data">0.00</td>

                  <td class="waki_gross_rev_data">0.00</td>

                  <td class="month_end_data">0.00</td>

                  <td class="comparison_mo_data">100.00</td>
                  <td class="comparison_cost_data">100.00</td>
                  <td class="comparison_rev_data">100.00</td>
                  <td class="weekly_caps_data">0</td>
                  <td class="percent_caps_data">0</td>


                  <td class="roiprice_mo_data">0</td>
                  <td class="monthly_arpu_data">0</td>
                  <td class="roi_data_1">0</td>
                  <td class="subs_data">0</td>
                  <td class="average_daily_rev_data">0</td>
                  <td class="average_daily_cost_data">0</td>
                  <td class="average_daily_pnl_data">0</td>
                  <td class="average_daily_mo_data">0</td>
                  <td class="average_daily_unreg_data">0</td>
                </tr>
                <tr class="roiRow">
                  <td>LK - Lka-dialog-dotjo</td>

                  <td class="mo_data_0">0.00</td>
                  <td class="cost_data_0">0.00</td>
                  <td class="rev_data_0">0.00</td>
                  <td class="mo_data_1">0.00</td>
                  <td class="cost_data_1">0.00</td>
                  <td class="rev_data_1">0.00</td>

                  <td class="pnl_data">0.00</td>

                  <td class="unreg_data">0.00</td>

                  <td class="month_end_spending_data">0.00</td>

                  <td class="waki_gross_rev_data">0.00</td>

                  <td class="month_end_data">0.00</td>

                  <td class="comparison_mo_data">100.00</td>
                  <td class="comparison_cost_data">100.00</td>
                  <td class="comparison_rev_data">100.00</td>
                  <td class="weekly_caps_data">0</td>
                  <td class="percent_caps_data">0</td>


                  <td class="roiprice_mo_data">0</td>
                  <td class="monthly_arpu_data">0</td>
                  <td class="roi_data_1">0</td>
                  <td class="subs_data">0</td>
                  <td class="average_daily_rev_data">0</td>
                  <td class="average_daily_cost_data">0</td>
                  <td class="average_daily_pnl_data">0</td>
                  <td class="average_daily_mo_data">0</td>
                  <td class="average_daily_unreg_data">0</td>
                </tr>
                <tr class="roiRow">
                  <td>SD - Sdn-mtn-dotjo</td>

                  <td class="mo_data_0">0.00</td>
                  <td class="cost_data_0">0.00</td>
                  <td class="rev_data_0">0.00</td>
                  <td class="mo_data_1">0.00</td>
                  <td class="cost_data_1">0.00</td>
                  <td class="rev_data_1">0.00</td>

                  <td class="pnl_data">0.00</td>

                  <td class="unreg_data">0.00</td>

                  <td class="month_end_spending_data">0.00</td>

                  <td class="waki_gross_rev_data">0.00</td>

                  <td class="month_end_data">0.00</td>

                  <td class="comparison_mo_data">100.00</td>
                  <td class="comparison_cost_data">100.00</td>
                  <td class="comparison_rev_data">100.00</td>
                  <td class="weekly_caps_data">0</td>
                  <td class="percent_caps_data">0</td>


                  <td class="roiprice_mo_data">0</td>
                  <td class="monthly_arpu_data">0</td>
                  <td class="roi_data_1">0</td>
                  <td class="subs_data">0</td>
                  <td class="average_daily_rev_data">0</td>
                  <td class="average_daily_cost_data">0</td>
                  <td class="average_daily_pnl_data">0</td>
                  <td class="average_daily_mo_data">0</td>
                  <td class="average_daily_unreg_data">0</td>
                </tr>
                <tr class="roiRow">
                  <td>NO - Nor-all-linkit</td>

                  <td class="mo_data_0">1.00</td>
                  <td class="cost_data_0">0.00</td>
                  <td class="rev_data_0">3.02</td>
                  <td class="mo_data_1">0.00</td>
                  <td class="cost_data_1">0.00</td>
                  <td class="rev_data_1">0.00</td>

                  <td class="pnl_data">0.00</td>

                  <td class="unreg_data">0.00</td>

                  <td class="month_end_spending_data">0.00</td>

                  <td class="waki_gross_rev_data">0.00</td>

                  <td class="month_end_data">0.00</td>

                  <td class="comparison_mo_data">100.00</td>
                  <td class="comparison_cost_data">100.00</td>
                  <td class="comparison_rev_data">100.00</td>
                  <td class="weekly_caps_data">0</td>
                  <td class="percent_caps_data">0</td>


                  <td class="roiprice_mo_data">0</td>
                  <td class="monthly_arpu_data">0</td>
                  <td class="roi_data_1">0</td>
                  <td class="subs_data">0</td>
                  <td class="average_daily_rev_data">0.43</td>
                  <td class="average_daily_cost_data">0</td>
                  <td class="average_daily_pnl_data">0</td>
                  <td class="average_daily_mo_data">0.14</td>
                  <td class="average_daily_unreg_data">0</td>
                </tr>
                <tr class="roiRow">
                  <td>IG - Irq-zain-linkit</td>

                  <td class="mo_data_0">1,132.00</td>
                  <td class="cost_data_0">377.62</td>
                  <td class="rev_data_0">30.68</td>
                  <td class="mo_data_1">201.00</td>
                  <td class="cost_data_1">83.70</td>
                  <td class="rev_data_1">0.00</td>

                  <td class="pnl_data">-83.70</td>

                  <td class="unreg_data">14.00</td>

                  <td class="month_end_spending_data">461.32</td>

                  <td class="waki_gross_rev_data">25.11</td>

                  <td class="month_end_data">7.53</td>

                  <td class="comparison_mo_data">-463.18</td>
                  <td class="comparison_cost_data">-351.16</td>
                  <td class="comparison_rev_data">100.00</td>
                  <td class="weekly_caps_data">0</td>
                  <td class="percent_caps_data">0</td>


                  <td class="roiprice_mo_data">0.42</td>
                  <td class="monthly_arpu_data">0</td>
                  <td class="roi_data_1">0</td>
                  <td class="subs_data">201.00</td>
                  <td class="average_daily_rev_data">4.38</td>
                  <td class="average_daily_cost_data">53.95</td>
                  <td class="average_daily_pnl_data">0.08</td>
                  <td class="average_daily_mo_data">161.71</td>
                  <td class="average_daily_unreg_data">2.00</td>
                </tr>
              </tbody>
            </table>
          </div>
        </div>

        {{-- <div class="d-flex align-items-center my-2 pull-right">
          <span class="badge badge-secondary px-2 bg-primary" id="loadTimer">Load Time :{{ round(microtime(true) - LARAVEL_START, 3) }}s</span> --}}
        </div>
        <button type="button" id="button" class="btn btn-danger btn-floating btn-sm btn-back-to-top-position up"><i class="fa fa-arrow-up"></i></button>


      </div>

{{--       <div class="sidenav-mask mask-body d-xl-none" data-action="sidenav-unpin" data-target="#sidenav-main"></div>
      <div class="sidenav-mask mask-body d-xl-none" data-action="sidenav-unpin" data-target="#sidenav-main"></div>
      <div class="sidenav-mask mask-body d-xl-none" data-action="sidenav-unpin" data-target="#sidenav-main"></div>
      <div class="sidenav-mask mask-body d-xl-none" data-action="sidenav-unpin" data-target="#sidenav-main"></div>
    </div>
    </div>

    <div class="modal fade" id="commonModal" tabindex="-1" role="dialog" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered" role="document">
        <div class="modal-content">
        <div>
            <h4 class="h4 font-weight-400 float-left modal-title"></h4>
            <a href="#" class="more-text widget-text float-right close-icon" data-dismiss="modal"
            aria-label="Close">Close</a>
        </div>
        <div class="modal-body">
        </div>
        </div>
    </div>
    </div>

    <div id="omnisearch" class="omnisearch">
    <div class="container">
        <div class="omnisearch-form">
        <div class="form-group">
            <div class="input-group input-group-merge input-group-flush">
            <div class="input-group-prepend">
                <span class="input-group-text"><i class="fas fa-search"></i></span>
            </div>
            <input type="text" class="form-control search_keyword"
                placeholder="Type and search By Deal, Lead and Tasks.">
            </div>
        </div>
        </div>
        <div class="omnisearch-suggestions">
        <div class="row">
            <div class="col-sm-12">
            <ul class="list-unstyled mb-0 search-output text-sm">
                <li>
                <a class="list-link pl-4" href="#">
                    <i class="fas fa-search"></i>
                    <span>Type and search By Deal, Lead and Tasks.</span>
                </a>
                </li>
            </ul>
            </div>
        </div>
        </div> --}}


@endsection
