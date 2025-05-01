<?php
//Key is the name of the item, value the influence cost/1000

$list=array("decurion_barrel" => 20,
"decurion_receiver" => 20,
"phaedra_barrel" => 20,
"corvas_barrel" => 20,
"corvas_receiver" => 20,
"cyngas_barrel" => 20,
"cyngas_receiver" => 20,
"fluctus_barrel" => 20,
"velocitus_barrel" => 20,
"centaur_aegis" => 20,
"gilded_truth" => 25,
"blade_of_truth" => 25,
"avenging_truth" => 25,
"stinging_truth" => 25,
"seeking_shuriken" => 25,
"smoke_shadow" => 25,
"fatal_teleport" => 25,
"rising_storm" => 25,
"peaceful_provocation" => 25,
"sonic_fracture" => 25,
"resonance" => 25,
"resonating_quake" => 25,
"elusive_retribution" => 25,
"endless_lullaby" => 25,
"reactive_storm" => 25,
"afterburn" => 25,
"everlasting_ward" => 25,
"guardian_armor" => 25,
"vexing_retaliation" => 25,
"guided_effigy" => 25,
"duality" => 25,
"calm_and_frenzy" => 25,
"energy_transfer" => 25,
"surging_dash" => 25,
"radiant_finish" => 25,
"furious_javelin" => 25,
"warriors_rest" => 25,
"biting_frost" => 25,
"freeze_force" => 25,
"ice_wave_impedance" => 25,
"chilling_globe" => 25,
"icy_avalanche" => 25,
"shattered_storm" => 25,
"mending_splinters" => 25,
"spectrosiphon" => 25,
"mach_crash" => 25,
"thermal_transfer" => 25,
"coil_recharge" => 25,
"cathode_current" => 25,
"tribunal" => 25,
"warding_thurible" => 25,
"lasting_covenant" => 25,
"balefire_surge" => 25,
"blazing_pillage" => 25,
"aegis_gale" => 25,
"viral_tempest" => 25,
"tidal_impunity" => 25,
"rousing_plunder" => 25,
"pilfering_swarm" => 25,
"elemental_sandstorm" => 25,
"negation_swarm" => 25,
"desiccations_curse" => 25,
"empowered_quiver" => 25,
"piercing_navigator" => 25,
"infiltrate" => 25,
"concentrated_arrow" => 25,
"rift_haven" => 25,
"rift_torrent" => 25,
"cataclysmic_continuum" => 25,
"savior_decoy" => 25,
"hushed_invisibility" => 25,
"safeguard_switch" => 25,
"irradiating_disarm" => 25,
"damage_decoy" => 25,
"hall_of_malevolence" => 25,
"explosive_legerdemain" => 25,
"total_eclipse" => 25,
"pyroclastic_flow" => 25,
"reaping_chakram" => 25,
"safeguard" => 25,
"controlled_slide" => 25,
"divine_retribution" => 25,
"neutron_star" => 25,
"antimatter_absorb" => 25,
"escape_velocity" => 25,
"molecular_fission" => 25,
"mind_freak" => 25,
"pacifying_bolts" => 25,
"chaos_sphere" => 25,
"assimilate" => 25,
"partitioned_mallet" => 25,
"conductor" => 25,
"repair_dispensary" => 25,
"temporal_erosion" => 25,
"temporal_artillery" => 25,
"wrecking_wall" => 25,
"thrall_pact" => 25,
"mesmer_shield" => 25,
"blinding_reave" => 25,
"shadow_haze" => 25,
"dark_propagation" => 25,
"axios_javelineers" => 25,
"intrepid_stand" => 25,
"tesla_bank" => 25,
"photon_repeater" => 25,
"repelling_bastille" => 25,
"shock_trooper" => 25,
"shocking_speed" => 25,
"transistor_shield" => 25,
"capacitance" => 25,
"fused_reservoir" => 25,
"critical_surge" => 25,
"celestial_stomp" => 25,
"enveloping_cloud" => 25,
"primal_rage" => 25,
"vampiric_grasp" => 25,
"the_relentless_lost" => 25,
"merulina_guardian" => 25,
"loyal_merulina" => 25,
"surging_blades" => 25,
"entropy_spike" => 25,
"entropy_flight" => 25,
"entropy_detonation" => 25,
"entropy_burst" => 25,
"vaykor_hek" => 125,
"vaykor_marelok" => 100,
"vaykor_sydon" => 125,
"synoid_heliocor" => 125,
"synoid_simulor" => 125,
"synoid_gammacor" => 100);
//array to safe all average prices in
$prices = [];
//Limit to three requests per second due to APIs rate limiting
$count = 0;
$ch = curl_init();
//Return our curl result
curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
//This is unsafe, but we don't send or receive sensitive data
curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, false);
curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);


foreach($list as $name => $cost) {
    //Set url and execute curl
    curl_setopt($ch, CURLOPT_URL, "https://api.warframe.market/v2/orders/item/" . $name . "/top");
    $response = curl_exec($ch);
    if (curl_errno($ch)) {
        echo('Error on ' .$name . ": " . curl_error($ch));
    } else {
        try{
        $data = json_decode($response, true);
        $sell = $data['data']['sell'];
        //In case sell is empty, for example because the name is wrong
        if (is_null($sell)) throw new Exception("Sell is empty for " . $name);
        $price = 0;
        //Maximum number of considered prices. Sell has maximum 5 offers, but the supply is frequently too low with extreme differences in prices.
        //So the size is set to 2 for now, to ignore unrealistic prices but still recognize very low supply that can be capitalised on
        //$size = sizeof($sell);
        $size = 2;
        for ($i=0; $i<=$size-1; $i++){
            $price += $sell[$i]['platinum']; 
        }
        //Find the average price, then devide it by the cost to find the average plat per 1000 reputation
        $prices[$name] = ($price/$size)/$cost;
    } catch(Exception $e) {
        echo 'Error-Message: ' .$e->getMessage();
      }
    }
    //Api is rate limited to 3 requests per second, this makes sure that well will never reach that limit
    $count++;
    if ($count >= 3) {
        sleep(1);
        $count = 0;
    }
}
//Sort in descending orders by price
arsort($prices);

foreach($prices as $name => $price){
    echo ($name . " : " .$price . "\n");
}


curl_close($ch);
?>