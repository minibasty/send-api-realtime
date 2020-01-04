<?php 
        //";6007641200100638801=201219960426=?+             12            1            0064560  20300                     ?"
        //"%  ^PRAKET$VEERAPOL$MR.^^?+             24            1            0028154  20300                     ?"
        $licenseParam =";6007641200100638801=201219960426=?+             12            1            0064560  20300                     ?";
        $license = explode("?",$licenseParam);
        $license = $license['1']; //"+             12            1            0064560  20300                     "
        $license = explode("+",$license);
        $license = $license['1']; //"             12            1            0064560  20300                     "
        print_r($license);
?>