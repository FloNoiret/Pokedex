<?php
require "./PokemonsManager.php";

$pokemonManager = new PokemonsManager();
$pokemonManager->delete($_GET["id"]);

// Redirect to index page after deleting
header("Location: ./index.php");
?>