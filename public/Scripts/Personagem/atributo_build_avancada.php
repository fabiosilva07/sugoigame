<?php
$valida = "EquipeSugoiGame2012";
require "../../Includes/conectdb.php";

$protector->need_tripulacao();
$protector->must_be_out_of_any_kind_of_combat();

$cod = $protector->get_number_or_exit("cod");
$atr = $protector->get_number_or_exit("atr");
$quant = $protector->get_number_or_exit("quant");

if ($atr < 1 || $atr > 8) {
    $protector->exit_error("Atributo inválido");
}
$atr = nome_atributo_tabela($atr);

$pers = $userDetails->get_pers_by_cod($cod);
if (! $pers) {
    $protector->exit_error("Personagem inválido");
}

if ($quant < 1 || ($quant - $pers[$atr]) > $pers["pts"]) {
    $protector->exit_error("Quantidade inválida");
}

$hp_razao = $pers["hp"] / $pers["hp_max"];
$mp_razao = $pers["mp"] / $pers["mp_max"];

$dif = $quant - $pers[$atr];
$pers[$atr] = $quant;
$pers["pts"] -= $dif;

$hp_max = (($pers["lvl"] - 1) * HP_POR_NIVEL) + HP_INICIAL + (($pers["vit"] - 1) * HP_POR_VITALIDADE);
$mp_max = (($pers["lvl"] - 1) * 7) + 100 + (($pers["vit"] - 1) * 7);

$hp = floor($hp_max * $hp_razao);
$mp = floor($mp_max * $mp_razao);

$connection->run("UPDATE tb_personagens SET hp=?, hp_max=?, mp=?, mp_max=?, atk=?, def=?, agl=?, res=?, pre=?, dex=?, con=?, vit=?, pts=? WHERE cod=?",
    "iiiiiiiiiiiiii", array($hp, $hp_max, $mp, $mp_max,
        $pers["atk"], $pers["def"], $pers["agl"], $pers["res"], $pers["pre"], $pers["dex"],
        $pers["con"], $pers["vit"], $pers["pts"], $pers["cod"]));

echo ":";
