<?php

class MaClasse
{
    public $a;
}

function increment(MaClasse $objet)
{
    $objet->a++;
}

$instance = new MaClasse;
$instance->a = 1;

increment($instance);

echo $instance->a;
