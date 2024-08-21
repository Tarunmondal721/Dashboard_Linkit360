<?php

return [



    'api_url' => [

        /*
         * Thirdparty ferry data Api  url.
         */
        'operator' => 'http://149.129.252.221:8028/app/api/rpt/third.php?req=listoperator&acc=admin',
        'service' => 'http://149.129.252.221:8028/app/api/rpt/third.php?req=ServiceList&acc=admin',
        'service_name' => 'http://149.129.252.221:8028/app/api/rpt/third.php?req=servicebyidservice&data=',
        'gross_revenue' => 'http://149.129.252.221:8028/app/api/rpt/third.php?req=DetailRevbyservice&acc=admin&data=',
        'service_mt_' => "http://149.129.252.221:8028/app/api/rpt/third.php?req=DetailStatusTrxbyservice&acc=admin&data=",

        'service_fmt_' => "http://149.129.252.221:8028/app/api/rpt/third.php?req=DetailStatusTrx1STPushbyservice&acc=admin&data=",

        'service_unReg' => "http://149.129.252.221:8028/app/api/rpt/third.php?req=DetailRegUnregbyservice&acc=admin&data=",
        'service_active' => "http://149.129.252.221:8028/app/api/rpt/third.php?req=DetailSubsActivebyservice&acc=admin&data=",
        'service_purged' => "http://149.129.252.221:8028/app/api/rpt/third.php?req=DetailPurged&acc=admin&data=",

    ]



];
