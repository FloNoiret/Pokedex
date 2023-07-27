<!-- header -->
<?php
require("./layout/header.php"); ?>


<body>
    <!-- navbar -->
    <?php require("./layout/navbar.php") ?>

    <?php
    require('./PokemonsManager.php');
    $manager = new PokemonsManager();
    require('./ImagesManager.php');
    $imagesManager = new ImagesManager();
    $pokemons = $manager->getAll();
    ?>

    <main class="container">
        <section class="d-flex flex-wrap justify-content-center">
            <?php foreach ($pokemons as $pokemon) : ?>
                <div class="card m-4" style="width: 18rem;">
                    <img src="<?= $imagesManager->get($pokemon->getImage())->getPath() ?>" class="card-img-top" alt="<?= $pokemon->getName() ?>">
                    <div class="card-body">
                        <h5 class="card-title"><?= $pokemon->getNumber() ?># <?php echo $pokemon->getName() ?></h5>
                        <p class="card-text"><?= $pokemon->getDescription() ?></p>
                        <a href="./update.php?id=<?= $pokemon->getId() ?>" class="btn btn-warning">Modifier</a>
                        <a href="./delete.php?id=<?= $pokemon->getId() ?>" class="btn btn-danger">Supprimer</a>

                    </div>
                </div>
            <?php endforeach ?>
        </section>
        <a href="./create.php" class="btn btn-success">Ajouter un Pokémon</a>
    </main>

    <!-- footer -->
    <?php require("./layout/footer.php") ?>

    </body>

</html>