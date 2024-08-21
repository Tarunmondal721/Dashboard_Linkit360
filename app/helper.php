<?php
use Carbon\Carbon;

if (!function_exists('getDateDiff')){
  function getDateDiff($start_date,$end_date){
    $today = Carbon::now()->format('Y-m-d');
    $created = new Carbon($start_date);
    $created_format = $created->format('Y-m-d');
    $dayscount = ($created->diff($end_date)); 
    $diff = $dayscount->days;

    if($today > $end_date)
    $diff = $dayscount->days + 1;
  
    return $diff;
  }
}

if (!function_exists('numberConverter')){
  function numberConverter($number,$decimal=0,$postion="pre",$symbol="" ){

 //return $number;




      if(($number == 0) || ($number == 0.00)){

        //return "ERR";

      }


        if($number>=1 && $number<=100){

//echo "decimal=".$decimal;
          $number= round($number,$decimal);//number_format(round($number,$decimal),$decimal);
          

        }

       elseif($number>=.09 && $number<1){

          //echo "decimal=".$decimal;
                    $number= round($number,$decimal);//number_format(round($number,$decimal),$decimal);
                    
          
                  }

                  elseif($number < .09){

                    //echo "decimal=".$decimal;
                              $number= round($number,$decimal);//number_format(round($number,$decimal),$decimal);
                              
                    
                            }


        elseif($number>100)
        {

      $number= number_format(round($number));
       

        }



        if(!empty($symbol)){
          if($postion=='post'){
            $number = $number.$symbol;
          }else{
            $number = $symbol.$number;
          }
        }

           




        

  return $number;
}
}

?>